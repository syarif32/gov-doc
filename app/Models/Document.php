<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'folder_id',
        'owner_id',
        'title',
        'description',
        'file_path',
        'extension',
        'google_file_id',
        'file_size',
        'version'
    ];
    public function getGoogleEditorUrlAttribute()
    {
        if (!$this->google_file_id) return null;

        $ext = strtolower($this->extension);
        if (in_array($ext, ['xls', 'xlsx', 'csv'])) {
            return "https://docs.google.com/spreadsheets/d/{$this->google_file_id}/edit?embedded=true";
        } elseif (in_array($ext, ['ppt', 'pptx'])) {
            return "https://docs.google.com/presentation/d/{$this->google_file_id}/edit?embedded=true";
        } else {
            return "https://docs.google.com/document/d/{$this->google_file_id}/edit?embedded=true";
        }
    }
    public function folder()
    {
        // Dokumen ini termasuk dalam folder apa?
        return $this->belongsTo(Folder::class, 'folder_id');
    }
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function permissions()
    {
        return $this->hasMany(DocumentPermission::class);
    }

    // Check if a specific user can Edit/Write
    public function canWrite($user)
    {
        return $this->owner_id === $user->id ||
            $this->permissions()->where('user_id', $user->id)
            ->where('access_level', 'write')
            ->exists();
    }
}
