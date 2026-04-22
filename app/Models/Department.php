<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Department extends Model
{
    protected $fillable = ['name_tk', 'name_ru', 'name_en'];

    public function getNameAttribute()
    {
        $lang = app()->getLocale();
        return $this->{"name_{$lang}"};
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
    public function folders(): HasMany
    {
        // Parameter kedua 'department_id' memastikan Laravel mencari di kolom yang tepat
        return $this->hasMany(Folder::class, 'department_id');
    }
}
