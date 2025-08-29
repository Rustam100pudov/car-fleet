<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    protected $fillable = ['car_id','user_id','starts_at','ends_at','status','purpose'];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at'   => 'datetime',
    ];

    public function car(): BelongsTo  { return $this->belongsTo(Car::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
