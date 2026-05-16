<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable {
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'name',
        'image',
        'email',
        'password',
        'google_id',
        'role',
        'status',
        'date_of_birth',
        'national_id',
        'phone_number',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected function casts(): array {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function UseCases() {
        return $this->hasMany( UseCase::class, 'user_id' );
    }

    public function posts() {
        return $this->hasMany( Post::class );
    }

    public function conversations() {
        return $this->hasMany( Conversation::class );
    }

    public function comments() {
        return $this->hasMany( Comment::class );
    }

    public function likes() {
        return $this->hasMany( Like::class );
    }

    public function isAdmin(): bool {
        return $this->role === 'admin';
    }

    public function isDoctor(): bool {
        return $this->role === 'doctor';
    }

    public function isUser(): bool {
        return $this->role === 'user';
    }

    public function hasRole( $roles ): bool {
        if ( is_array( $roles ) ) {
            return in_array( $this->role, $roles );
        }

        return $this->role === $roles;
    }

    public function dashboardRoute(): string {
        return match ( $this->role ) {
            'admin' => '/dashboard',
            'doctor' => '/doctor/dashboard',
            default => '/',
        }
        ;
    }
}
