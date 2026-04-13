<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = ['title', 'type']; // type: 'private' or 'group'

    // Users in this conversation
    public function users()
    {
        return $this->belongsToMany(User::class, 'conversation_user')
            ->withPivot('is_admin')
            ->withTimestamps();
    }

    // Messages in this conversation
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    // Get the latest message (for the chat list)
    public function lastMessage()
    {
        return $this->hasOne(Message::class)->latest();
    }
}
