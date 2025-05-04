<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{

    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'user';

    protected $fillable = [
        'email',
        'mobile',
        'password',
        'firebaseToken',
        'updatedAt',
        'createdAt',
    ];

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';


    public function account()
    {
        return $this->hasOne(Account::class, 'userId');
    }


}

