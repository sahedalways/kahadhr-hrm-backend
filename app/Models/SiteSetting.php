<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;


class SiteSetting extends Model
{
    protected $fillable = [
        'company_id',
        'site_title',
        'logo',
        'favicon',
        'hero_image',
        'site_phone_number',
        'site_email',
        'copyright_text',
    ];

    protected $appends = ['hero_image_url', 'favicon_url', 'logo_url'];

    // Accessor for logo_url
    public function getLogoUrlAttribute()
    {
        return $this->logo
            ? getFileUrl('image/settings/logo.' . $this->logo)
            : asset('assets/img/default-image.jpg');
    }

    // Accessor for favicon_url
    public function getFaviconUrlAttribute()
    {
        return $this->favicon
            ? getFileUrl('image/settings/favicon.' . $this->favicon)
            : asset('assets/img/default-favicon.ico');
    }


    public function getHeroImageUrlAttribute()
    {
        return getFileUrl(
            $this->hero_image ? 'image/settings/hero.' . $this->hero_image : null,
            'assets/img/default-hero.jpg'
        );
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }


    // Global Scope based on user type
    protected static function booted()
    {
        static::addGlobalScope('filterByUserType', function (Builder $builder) {
            if (!auth()->check()) {
                return;
            }

            $user = app('authUser');

            if ($user->user_type === 'superAdmin') {
                $builder->whereNull('company_id');
            } elseif ($user->user_type === 'company') {
                $builder->where('company_id', $user->company->id ?? 0);
            } elseif ($user->user_type === 'employee' || $user->user_type === 'teamLead') {
                $builder->whereHas('company.employees', function (Builder $query) use ($user) {
                    $query->where('id', $user->id);
                });
            }
        });
    }
}
