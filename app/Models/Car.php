<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Car extends Model
{
    protected $fillable = ['car_model_id','license_plate','vin','driver_id'];

    public function model(): BelongsTo { return $this->belongsTo(CarModel::class, 'car_model_id'); }
    public function driver(): BelongsTo { return $this->belongsTo(Driver::class); }
    public function bookings(): HasMany { return $this->hasMany(Booking::class); }

    /** Авто свободно, если нет брони, пересекающей интервал */
    public function scopeAvailableBetween(Builder $q, \DateTimeInterface $start, \DateTimeInterface $end): Builder
    {
        return $q->whereDoesntHave('bookings', function (Builder $b) use ($start, $end) {
            $b->whereIn('status', ['pending','approved'])
              ->where('starts_at', '<', $end)
              ->where('ends_at',   '>', $start);
        });
    }
}
