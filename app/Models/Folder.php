<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
   
    protected $fillable = ['name', 'parent_id', 'department_id', 'google_folder_id'];
    // Get the parent folder
    public function parent()
    {
        return $this->belongsTo(Folder::class, 'parent_id');
    }

    // Get sub-folders
    // app/Models/Folder.php

// Folder ini milik Departemen apa?
public function department()
{
    return $this->belongsTo(Department::class, 'department_id');
}

// Folder ini punya dokumen apa saja?
public function documents() {
    return $this->hasMany(Document::class);
}

// Untuk sistem folder bercabang (sub-folder)
public function children() {
    return $this->hasMany(Folder::class, 'parent_id');
}
}
