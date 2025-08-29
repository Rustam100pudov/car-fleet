<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CarModel extends Model
{
    protected $fillable = ['brand','model','comfort_category_id'];

    public function comfortCategory(): BelongsTo
    {
        return $this->belongsTo(ComfortCategory::class);
    }

    public function cars(): HasMany
    {
        return $this->hasMany(Car::class);
    }
}
