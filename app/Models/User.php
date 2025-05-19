<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['prefix', 'first_name', 'middle_name', 'last_name', 'full_name', 'name', 'email', 'google_id', 'email_verified_at', 'password', 'user_role', 'subscribe_status', 'phone', 'address', 'state', 'postal_code', 'country', 'date_of_birth', 'gender', 'age', 'is_active', 'last_login_at', 'profile_image', 'bio', 'social_media', 'city'];


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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // public function patient()
    // {
    //     return $this->hasOne(Patient::class, 'user_id');
    // }
    public function patient()
    {
        return $this->hasOne(Patient::class);
    }

    public function provider()
    {
        return $this->hasOne(Provider::class);
    }
}
