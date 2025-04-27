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



}
