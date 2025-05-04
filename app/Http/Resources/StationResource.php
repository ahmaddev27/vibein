<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'features' => $this->features,
            'categories' => $this->categories->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->CategoryTranslations->first()->name,
                ];
            }),

            'sort_order' => $this->sort_order,
            'is_recommended' => $this->is_recommended,
            'images' => $this->images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'image' => url('storage/'.$image->image),
                ];
            }),
        ];
    }
}
