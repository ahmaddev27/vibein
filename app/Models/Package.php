<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Package extends Model
{

    protected $table = 'packages';

    protected $fillable = [
        'name',
        'description',
        'price',
        'total',
        'status',
        'tags'

    ];


    public function products(): HasMany
    {
        return $this->hasMany(PackageProduct::class);
    }

    public function alternatives(): HasMany
    {
        return $this->hasMany(PackageProductAlternative::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(PackageImages::class);
    }


    public function toggleSelectedProduct(int $position, int $productAlternativeId): bool
    {
        DB::beginTransaction();

        try {
            $this->products()
                ->where('position', $position)
                ->update(['is_selected' => false]);

            $this->alternatives()
                ->where('position', $position)
                ->update(['is_selected' => false]);

            // Log matching alternative
            $alternative = $this->alternatives()
                ->where('position', $position)
                ->where('product_id', $productAlternativeId)
                ->first();

            if (!$alternative) {
                Log::warning("No matching alternative found", [
                    'package_id' => $this->id,
                    'position' => $position,
                    'product_alternative_id' => $productAlternativeId,
                ]);
            }

            $affected = $this->alternatives()
                ->where('position', $position)
                ->where('id', $productAlternativeId)
                ->update(['is_selected' => true]);

            DB::commit();
            return $affected > 0;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }





}
