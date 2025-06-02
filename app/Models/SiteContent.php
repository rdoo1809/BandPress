<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiteContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id',
        'logo',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(BandSite::class, 'site_id', 'id');
    }
}
