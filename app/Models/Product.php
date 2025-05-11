<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Builder;


class Product extends Model
{
    use HasFactory;

    protected $table = 'product';
    protected $fillable=[
        'status',
        'lable',
        'companyId',
        'brandId',
        'taxId',
        'updatedAt',
        'createdAt'
    ];

    // Custom timestamp column names
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected static function booted()
    {
        static::addGlobalScope('company', function (Builder $builder) {
            $builder->where('companyId', 31);
        });
    }

    public function images()
    {

        return $this->hasMany(ProductImages::class, 'product_id', 'id');
    }


    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class,'productCategories','productId','categoryId')->where('subCategory',false);;
    }

    public function Subcategories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class,'productCategories','productId','categoryId')->where('subCategory',true);
    }

    public function productVariants()
    {
        return $this->hasMany(ProductVariants::class, 'productId', 'id');
    }

    public function productTranslations()
    {
        return $this->hasMany(ProductTranslations::class, 'productId', 'id');
    }


    public function Brand()
    {
        return $this->belongsTo(Brand::class, 'brandId', 'id');
    }

    public function productTax()
    {
        return $this->belongsTo(Tax::class, 'taxId', 'id');
    }



    public function getLinksAttribute()
    {
        $links = $this->attributes['links'] ?? '';

        // Remove curly braces if present
        $links = trim($links, '{}');

        // Check if it's a JSON-encoded array
        $decodedLinks = json_decode($links, true);

        // If it's an array, return it
        if (is_array($decodedLinks)) {
            return $decodedLinks;
        }

        // Otherwise, return it as an array with a single string
        return !empty($links) ? [$links] : [];
    }


    public function getOptionsAttribute()
    {
        $options = $this->attributes['options'] ?? '';

        // If empty, return empty array
        if (empty($options)) {
            return [];
        }

        // Remove escaped quotes if present
        $options = stripslashes($options);

        // Handle cases where the string might be wrapped in extra quotes
        $options = trim($options, '"');

        try {
            $decoded = json_decode($options, true);

            // If decoding fails, try to fix common formatting issues
            if (json_last_error() !== JSON_ERROR_NONE) {
                // Try removing any extra escaping
                $options = str_replace('\"', '"', $options);
                $decoded = json_decode($options, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    // If still failing, log the error and return empty array
                    Log::error('Failed to decode options JSON', [
                        'original' => $this->attributes['options'],
                        'error' => json_last_error_msg()
                    ]);
                    return [];
                }
            }

            return $decoded;
        } catch (\Exception $e) {
            Log::error('Error processing options attribute', [
                'error' => $e->getMessage(),
                'options' => $options
            ]);
            return [];
        }
    }


    public function tax()
    {
        return $this->hasOne(Tax::class, 'id', 'taxId');
    }

//    public function getLinksAttribute()
//    {
//
//        return json_decode($this->attributes['links'],true);
//
//    }

}
