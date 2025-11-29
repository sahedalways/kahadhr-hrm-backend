<?php

namespace App\Models;

use App\Traits\Scopes\FilterByUserType;
use Illuminate\Database\Eloquent\Model;

class ChatGroup extends Model
{
    use FilterByUserType;
    protected $fillable = ['company_id', 'name', 'created_by', 'team_id'];

    public function company()
    {
        return $this->belongsTo(User::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'group_id');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'chat_group_members', 'group_id', 'user_id');
    }

    public function teams()
    {
        return $this->hasMany(Team::class, 'team_id');
    }
}
