<?php

namespace App\Http\Controllers\Document;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\User;
use App\Models\DocumentPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    /**
     * Display a listing of the documents.
     */
    public function index()
    {
        $user = auth()->user();

        if ($user->role_level === 'admin') {
            // Administrators see EVERY document in the system
            $documents = Document::with('owner')->latest()->paginate(20);
        } else {
            // Normal users see only their own OR those shared with them
            $documents = Document::where('owner_id', $user->id)
                ->orWhereHas('permissions', function ($q) use ($user) {
                    $q->where('user_id', $user->id)->orWhere('department_id', $user->department_id);
                })
                ->with('owner')
                ->latest()
                ->paginate(20);
        }

        // List of users for the "Share" dropdown (excluding self)
        $users = User::where('id', '!=', $user->id)->get();

        return view('documents.index', compact('documents', 'users'));
    }

    /**
     * Store a newly uploaded document in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|file|max:51200', // 50MB Max
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');

            // Store in 'private' folder - not accessible via public URL
            $path = $file->store('private/documents');

            $doc = Document::create([
                'title' => $request->title,
                'file_path' => $path,
                'extension' => $file->getClientOriginalExtension(),
                'file_size' => $file->getSize(),
                'owner_id' => auth()->id(),
            ]);

            auth()->user()->logAction("Uploaded document: " . $doc->title);

            return back()->with('success', __('Document uploaded successfully'));
        }

        return back()->with('error', 'File upload failed');
    }

    /**
     * Download the file if the user has permission.
     */
    public function download(Document $document)
    {
        $user = auth()->user();

        // Permission Logic:
        // 1. Is Admin? (Full access)
        // 2. Is Owner?
        // 3. Is it shared with this user in the permissions table?
        $hasPermission = DocumentPermission::where('document_id', $document->id)
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)->orWhere('department_id', $user->department_id);
            })->exists();

        if ($user->role_level !== 'admin' && $document->owner_id !== $user->id && !$hasPermission) {
            auth()->user()->logAction("UNAUTHORIZED DOWNLOAD ATTEMPT: " . $document->title);
            abort(403, 'You do not have permission to download this file.');
        }

        auth()->user()->logAction("Downloaded document: " . $document->title);

        // Download from private storage
        return Storage::download($document->file_path, $document->title . '.' . $document->extension);
    }

    /**
     * Show the form for editing the document metadata.
     */
    public function edit(Document $document)
    {
        // Only owner or admin can edit metadata
        if ($document->owner_id !== auth()->id() && auth()->user()->role_level !== 'admin') {
            abort(403, 'Unauthorized access.');
        }

        return view('documents.edit', compact('document'));
    }

    /**
     * Update the document metadata.
     */
    public function update(Request $request, Document $document)
    {
        if ($document->owner_id !== auth()->id() && auth()->user()->role_level !== 'admin') {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $document->update([
            'title' => $request->title,
        ]);

        auth()->user()->logAction("Updated document info: " . $document->title);

        return redirect()->route('docs.index')->with('success', __('Document updated'));
    }

    /**
     * Remove the document from storage.
     */
    public function destroy(Document $document)
    {
        // Only owner or admin can delete
        if ($document->owner_id !== auth()->id() && auth()->user()->role_level !== 'admin') {
            abort(403, 'Unauthorized.');
        }

        // Delete the physical file
        Storage::delete($document->file_path);

        // Delete database record
        $document->delete();

        auth()->user()->logAction("Deleted document: " . $document->title);

        return back()->with('success', __('Document deleted successfully'));
    }

    /**
     * Share the document with another user or department.
     */
    public function share(Request $request, Document $document)
    {
        // Only owner can share their file
        if ($document->owner_id !== auth()->id()) {
            abort(403, 'Only the owner can share this document.');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'access_level' => 'required|in:read,write'
        ]);

        DocumentPermission::updateOrCreate(
            ['document_id' => $document->id, 'user_id' => $request->user_id],
            ['access_level' => $request->access_level]
        );

        auth()->user()->logAction("Shared {$document->title} with user ID: " . $request->user_id);

        return back()->with('success', __('Document shared successfully'));
    }
}
