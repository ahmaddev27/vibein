<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Onboarding extends Model
{
    protected $table = 'onbordings_app';
    protected $fillable = [
        'image',
        'description',
        'title',
    ];



    public function getImage()
    {
        $value = $this->image;
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

