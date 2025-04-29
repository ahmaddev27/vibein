<?php

namespace App\Http\Resources\dashboard;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);

        $data['images'] = $this->images->map(function ($image) {
            return [
                'id' => $image->id,
                'image' => url('storage/' . $image->image),
            ];
        });

        return $data;
    }
}
