<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MachineImages extends Model
{
    protected $table = 'machine_images';
    protected $fillable = [
        'machine_id',
        'image',
    ];

    public function machine()
    {
        return $this->belongsTo(Machine::class, 'machine_id');
    }


}
