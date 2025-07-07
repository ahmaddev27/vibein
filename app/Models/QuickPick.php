<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuickPick extends Model
{
    protected $table = 'quick_picks';
    protected $fillable = ['name', 'title', 'description', 'meta_title', 'meta_description','image'];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_quick_pick')
            ->withPivot('count')
            ->withTimestamps();
    }


}
