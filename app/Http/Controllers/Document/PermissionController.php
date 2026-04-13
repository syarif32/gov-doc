<?php

namespace App\Http\Controllers\Document;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentPermission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function store(Request $request, Document $document)
    {
        // Only owner can share
        if ($document->owner_id !== auth()->id()) abort(403);

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'access_level' => 'required|in:read,write'
        ]);

        DocumentPermission::updateOrCreate(
            ['document_id' => $document->id, 'user_id' => $request->user_id],
            ['access_level' => $request->access_level]
        );

        return back()->with('success', 'Permissions shared.');
    }
}
