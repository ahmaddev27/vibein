<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class CategoryTranslations extends Model
{
    use HasFactory;

    protected $table = 'categoryTranslation';

    protected $fillable = [
        'categoryId',
        'name',
        'description',
        'metaTagTitle',
        'metaTagDescription',
        'metaTagKeywords',
        'languageCode',
        'updatedAt',
        'createdAt'
    ];
    // Custom timestamp column names
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

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

}
