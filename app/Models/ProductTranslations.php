<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class ProductTranslations extends Model
{
    use HasFactory;
    protected $table = 'productTranslation';

    protected $fillable=[

        'name',
        'description',
        'productId',
        'languageCode',
        'shortDescription',
        'tags',
        'metaTagTitle',
        'metaTagDescription',
        'customTab',
        'createdAt',
        'updatedAt',

    ];

    // Custom timestamp column names
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';




    public function getTagsAttribute()
    {
        $tags = $this->attributes['tags'] ?? '[]'; // Default to empty array JSON string

        // Decode the JSON string to a PHP array
        $tagArray = json_decode($tags, true);

        // If json_decode fails (returns null), return empty array
        return $tagArray ?? [];
    }
}
