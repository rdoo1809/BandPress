<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BandEvent extends Model
{
    protected $fillable = [
        'band_site_id',
        'name',
        'day',
        'month',
        'description',
        'venue_link'
    ];

    public function bandSite(): BelongsTo
    {
        return $this->belongsTo(BandSite::class, 'band_site_id');
    }
}
