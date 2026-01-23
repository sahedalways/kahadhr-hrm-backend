<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ChatMessage extends Model
{
    protected $fillable = ['group_id', 'sender_id', 'receiver_id', 'message', 'media_path', 'company_id', 'team_id', 'is_read'];

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

    public function reads()
    {
        return $this->hasMany(ChatMessageRead::class, 'message_id');
    }


    /**
     * Get full URL for media attachment
     *
     * @return string|null
     */
    public function getAttachmentUrlAttribute()
    {
        if (!$this->media_path) {
            return null;
        }


        if (Storage::disk('public')->exists($this->media_path)) {
            return asset('storage/' . $this->media_path);
        }
    }

    /**
     * Check type of attachment
     *
     * @return string|null ('image', 'video', 'gif', 'file', null)
     */
    public function getAttachmentTypeAttribute()
    {
        if (!$this->media_path) {
            return null;
        }

        $extension = strtolower(pathinfo($this->media_path, PATHINFO_EXTENSION));

        if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
            return 'image';
        } elseif (in_array($extension, ['mp4', 'mov', 'avi'])) {
            return 'video';
        } elseif ($extension === 'gif') {
            return 'gif';
        } else {
            return 'file';
        }
    }
}
