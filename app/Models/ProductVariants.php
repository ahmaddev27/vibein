<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class
ProductVariants extends Model
{
    use HasFactory;
    protected $table = 'productVariant';


    protected $fillable=[
        'SKU',
        'price',
        'quantity',
        'minimumQuantity',
        'title',
        'colorCode',
        'productId',
        'prices',
        'images',
        'createdAt',
        'updatedAt',
    ];


    // Custom timestamp column names
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    protected $casts = [
        'productSpecialPrice' => 'array',
        'prices' => 'array',
        'productSpecialQuantityPrice' => 'array',
        'searchKeys' => 'array',
        'images' => 'array',
    ];

    public function getImagesAttribute($value)
    {
        // If the value is empty, return an empty array
        if (empty($value)) {
            return [];
        }

        // Handle both formats: raw DB string or already converted array
        if (is_array($value)) {
            $imagePaths = $value; // Already converted by cast
        } else {
            // Handle raw DB string format (e.g., "{path1,path2}")
            $value = trim($value, '{}'); // Remove curly braces
            $imagePaths = $value ? array_map('trim', explode(',', $value)) : [];
        }

        // Base URL for images
        $baseUrl = url('/storage'); // Assuming images are stored in the "public/storage" directory

        // Check if each image path is already a URL, and prepend the base URL if not
        $fullUrls = array_map(function ($path) use ($baseUrl) {
            // Check if the path starts with "http://" or "https://"
            if (filter_var($path, FILTER_VALIDATE_URL)) {
                return $path; // Path is already a URL
            }
            // Prepend the base URL to the path
            return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
        }, $imagePaths);

        return $fullUrls;
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
