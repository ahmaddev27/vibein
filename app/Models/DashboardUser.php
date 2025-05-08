<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class DashboardUser extends Authenticatable
{

    use HasApiTokens;

    protected $table = 'dashboard_user';

    protected $fillable = [
        'email',
        'name',
        'avatar',
        'mobile',
        'password',
        'firebaseToken',

    ];

    protected static function booted()
    {
        static::addGlobalScope('company', function (Builder $builder) {
            $builder->where('companyId', env('DEFAULT_COMPANY_ID', 31)); // 1 كقيمة افتراضية
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

}
