<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $fillable = ['group_id', 'sender_id', 'receiver_id', 'message', 'media_path', 'company_id', 'team_id'];

    public function group()
    {
        return $this->belongsTo(ChatGroup::class, 'group_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function company()
    {
        return $this->belongsTo(User::class, 'company_id');
    }


    public function team()
    {
        return $this->belongsTo(User::class, 'team_id');
    }
}
