<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class SiteSetting extends Model
{
    protected $fillable = [
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
}