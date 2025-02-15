<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Session;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser, HasAvatar
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids;
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

    /**
     * Default values for attributes.
     *
     * @var array<string, object>
     */
    protected $attributes = [
        'groups' => '[]',
    ];

    public function application(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Application::class);
    }

    /**
     * @return bool true if user is part of the IDP group specified in ef.admin_group.
     */
    public function isAdmin(): bool
    {
        return !empty($this->groups) && in_array(config('convention.admin_group'), $this->groups);
    }

    /**
     * @return bool true if user is part of the IDP group specified in ef.frontdesk_group or an admin.
     */
    public function isFrontdesk(): bool
    {
        return !empty($this->groups) && $this->isAdmin() || in_array(config('convention.frontdesk_group'), $this->groups);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->isAdmin();
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return Session::get('avatar');
    }
}
