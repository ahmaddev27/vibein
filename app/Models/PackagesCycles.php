<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackagesCycles extends Model
{
    protected $table = 'packages_cycles';

    protected $fillable = [
        'package_id',
        'cycle_id',
        'price',
        'created_at',
        'updated_at',
    ];

    public $timestamps = true;

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

    public function cycle()
    {
        return $this->belongsTo(Cycle::class, 'cycle_id');
    }
}
