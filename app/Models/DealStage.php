<?php

namespace App\Models;

use Database\Factories\DealStageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DealStage extends Model
{
    /** @use HasFactory<DealStageFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'sort_order',
    ];

    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class);
    }
}
