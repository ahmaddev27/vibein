<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class ProductVariants extends Model
{
    use HasFactory;
    protected $table = 'productVariant';
//    public function category(){
//        return $this->belongsTo(Category::class,'category_id','id');
//    }
    protected $casts = [
        'productSpecialPrice' => 'array',
        'productSpecialQuantityPrice' => 'array',
        'searchKeys' => 'array',
    ];
    public function getImagesAttribute()
    {
        $images = $this->attributes['images'] ?? '';

        // Remove curly braces if present
        $images = trim($images, '{}');

        // Split by commas to get individual URLs
        $imageArray = explode(',', $images);

        // Trim spaces from each image URL
        return array_map('trim', $imageArray);
    }

    public function getNameAttribute()
    {
        $name = $this->attributes['name'] ?? '';

        // Handle case where it's already an array (from JSON)
        if (is_array($name)) {
            return $name;
        }

        // Handle JSON-encoded string (like your example "\"en\":\"red/xl\"")
        $decoded = json_decode($name, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }

        // Try to detect if it's a JSON string wrapped in extra quotes
        $stripped = trim($name, '"');
        $decoded = json_decode($stripped, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }

        // Fallback for simple string case
        return $name ? [$name] : [];
    }



    public function getSearchKeysAttribute()
    {
        $value = $this->attributes['searchKeys'] ?? '[]'; // Default to empty JSON array

        // Decode JSON and ensure we always return an array of items with title/description
        $decoded = json_decode($value, true) ?? [];

        return array_map(function ($item) {
            return [
                'title' => $item['title'] ?? null,
                'description' => $item['description'] ?? null
            ];
        }, is_array($decoded) ? $decoded : []);
    }



}
