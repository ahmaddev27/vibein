<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Sliders extends Model
{
    protected $fillable = ['image'];
    //
    protected $table = 'appSlider';


    public function getImageAttribute($value)
    {
        if (empty($value)) {
            return null;
        }
        // Check if file exists in storage
        if (Storage::disk('public')->exists($value)) {
            return asset(Storage::url($value));
        }

        // Fallback to asset path (for older files or different storage)
        return asset($value);
    }


}

