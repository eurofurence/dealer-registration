<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'identity_id',
        'reg_id',
        'password',
        'groups',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'groups' => 'array',
    ];

    protected $attributes = [
        'groups' => [],
    ];

    public function application(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Application::class);
    }

    public function isAdmin(): bool
    {
        return !empty($this->groups) && in_array(config('ef.admin_group'), $this->groups);
    }

    public function isFrontdesk(): bool
    {
        return !empty($this->groups) && $this->isAdmin() || in_array(config('ef.frontdesk_group'), $this->groups);
    }

    public function canAccessFilament(): bool
    {
        return $this->isAdmin();
    }
}
