<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/** Entitas pengguna platform dengan atribut profil dan role RBAC. */
final class User extends Authenticatable
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory, Notifiable;

    /** Nilai awal keamanan agar objek baru konsisten dengan default database. */
    protected $attributes = ['role' => 'user', 'is_active' => true];

    protected $hidden = ['password', 'remember_token'];

    protected $fillable = ['name', 'email', 'password', 'division', 'job_title', 'avatar'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime', 'password' => 'hashed', 'role' => UserRole::class,
            'points' => 'integer', 'level' => 'integer', 'current_streak' => 'integer',
            'last_active_date' => 'date', 'is_active' => 'boolean',
        ];
    }

    public function isAdmin(): bool { return $this->role === UserRole::ADMIN; }
}
