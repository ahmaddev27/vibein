<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class ProductTranslations extends Model
{
    use HasFactory;
    protected $table = 'productTranslation';
//    public function category(){
//        return $this->belongsTo(Category::class,'category_id','id');
//    }


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

    public function getTagsAttribute()
    {
        $tags = $this->attributes['tags'] ?? '[]'; // Default to empty array JSON string

        // Decode the JSON string to a PHP array
        $tagArray = json_decode($tags, true);

        // If json_decode fails (returns null), return empty array
        return $tagArray ?? [];
    }
}
