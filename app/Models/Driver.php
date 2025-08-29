<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Driver extends Model
{
    protected $fillable = ['full_name','phone'];

    public function cars(): HasMany
    {
        return $this->hasMany(Car::class);
    }
}
