<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BandRelease extends Model
{
    protected $fillable = [
        'band_site_id',
        'cover_image',
        'host_link'
    ];

    public function bandSite(): BelongsTo
    {
        return $this->belongsTo(BandSite::class, 'band_site_id');
    }
}
