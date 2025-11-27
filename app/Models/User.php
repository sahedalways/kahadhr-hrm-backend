<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasApiTokens, Notifiable;

    /**    '
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'f_name',
        'l_name',
        'phone_no',
        'email',
        'password',
        'permissions',
        'profile_completed',
        'email_verified_at',
        'phone_verified_at',
        'remember_token',
        'user_type',
        'is_active'
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
            'permissions' => 'array',
            'profile_completed' => 'boolean'
        ];
    }



    // Full Name Accessor
    public function getFullNameAttribute()
    {
        return $this->f_name . ' ' . $this->l_name;
    }



    // One User → One Company
    public function company()
    {
        return $this->hasOne(Company::class);
    }


    public function employee()
    {
        return $this->hasOne(Employee::class);
    }
}
