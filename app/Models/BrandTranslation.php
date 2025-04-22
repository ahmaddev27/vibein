<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BrandTranslation extends Model
{
    protected $table ='brandTranslation';

    protected $fillable = [
        'name',
        'description',
        'metaTagTitle',
        'metaTagDescription',
        'metaTagKeywords',
        'languageCode',
        'metaTagKeywords',
        'brandId',
        'updatedAt',
        'createdAt'
    ];

    // Custom timestamp column names
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brandId', 'id');
    }


}
