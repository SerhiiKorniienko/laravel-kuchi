<?php

namespace SerhiiKorniienko\LaravelKuchi\Tests;

use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * @property $id
 * @property $name
 * @property $email
 * @property $password
 */
class User extends Authenticatable
{
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /** @var string[] */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
