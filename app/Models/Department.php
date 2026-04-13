<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
