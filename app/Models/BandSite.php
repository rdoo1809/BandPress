<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BandSite extends Model
{
    use HasFactory;

    protected $fillable = [
        'repo_url',
        'deployment_url',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(BandEvent::class, 'band_site_id');
    }

    public function releases(): HasMany
    {
        return $this->hasMany(BandRelease::class, 'band_site_id');
    }
}
