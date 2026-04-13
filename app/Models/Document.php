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
        'file_size',
        'version'
    ];

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
