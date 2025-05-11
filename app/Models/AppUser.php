<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class AppUser extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'app_users';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'phone',
        'is_employee',
        'company_id',
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
        'email_verified_at' => 'date',
    ];

    protected static function booted()
    {
        static::addGlobalScope('company', function (Builder $builder) {
            $builder->where('companyId', env('DEFAULT_COMPANY_ID', 31));
        });

        static::saving(function ($model) {
            if (empty($model->companyId)) {
                $model->companyId = env('DEFAULT_COMPANY_ID', 31);
            }
        });
    }

    public function getAvatar()
    {
        if ($this->avatar) {
            return url('storage/' . $this->avatar);
        } else {
            return url('blank.png');
        }
    }


    public function fcm_tokens()
    {
        return $this->hasMany(FcmToken::class, 'user_id');
    }


//    public function notifications()
//    {
//        return $this->hasMany(Notification::class, 'user_id');
//    }
//

    public function routeNotificationForFcm($notification = null,)
    {
        return $this->fcm_tokens()->pluck('token')->toArray();
    }

    public function address()
    {

        return $this->hasMany(AppUserAddress::class, 'user_id');
    }

}
