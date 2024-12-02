<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\belongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\Common;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, Common;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'role'
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

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $this->encryptData($value);
    }

    public function getNameAttribute($value)
    {
        return $this->decryptData($value);
    }

    public function setPhoneNumberAttribute($value)
    {
        $this->attributes['phone_number'] = $this->encryptData($value);
    }

    public function getPhoneNumberAttribute($value)
    {
        return $this->decryptData($value);
    }

    public function city(): belongsTo
    {
        return $this->belongsTo(related: City::class);
    }
}
