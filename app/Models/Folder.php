<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    protected $fillable = ['name', 'parent_id', 'department_id'];

    // Get the parent folder
    public function parent()
    {
        return $this->belongsTo(Folder::class, 'parent_id');
    }

    // Get sub-folders
    public function children()
    {
        return $this->hasMany(Folder::class, 'parent_id');
    }

    // Get documents inside this folder
    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    // Link to department
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
