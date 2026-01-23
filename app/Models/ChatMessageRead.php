<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessageRead extends Model
{
    use HasFactory;


    protected $fillable = [
        'message_id',
        'user_id',
        'read_at',
    ];

    protected $dates = [
        'read_at',
    ];

    /**
     * The message that was read.
     */
    public function message()
    {
        return $this->belongsTo(ChatMessage::class, 'message_id');
    }

    /**
     * The user who read the message.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
