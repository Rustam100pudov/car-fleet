<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Атрибуты, которые можно массово заполнять.
     *
     * ВАЖНО: добавили position_id, чтобы можно было задавать должность
     * через updateOrCreate/Seeder/Tinker.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'position_id',
    ];

    /**
     * Атрибуты, скрытые при сериализации.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Касты атрибутов.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Связь с должностью.
     */
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }
}
