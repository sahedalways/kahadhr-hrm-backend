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
            $user = auth()->check() ? app('authUser') : null;

            if (!$user) {
                $builder->whereNull('company_id');
                return;
            }

            if ($user->user_type === 'superAdmin') {
                $builder->whereNull('company_id');
            } elseif ($user->user_type === 'company') {
                $builder->where('company_id', $user->company->id ?? 0);
            } elseif (in_array($user->user_type, ['employee', 'teamLead'])) {
                $builder->where('company_id', $user->employee->company->id ?? 0);
            }
        });


        static::created(function ($setting) {
            if (is_null($setting->company_id) && $setting->favicon) {
                self::whereNotNull('company_id')
                    ->update(['favicon' => $setting->favicon]);
            }
        });


        static::updated(function ($setting) {
            if (is_null($setting->company_id) && $setting->wasChanged('favicon')) {
                self::whereNotNull('company_id')
                    ->update(['favicon' => $setting->favicon]);
            }


            if ($setting->company_id === 0 && $setting->wasChanged('copyright_text')) {
                self::where('company_id', '>', 0)
                    ->update(['copyright_text' => $setting->copyright_text]);
            }
        });
    }
}
