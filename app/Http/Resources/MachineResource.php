<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MachineResource extends JsonResource
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
            'status' => $this->status,
            'size' => $this->size,
            'meta_title' => $this->meta_title,
            'category' => $this->category ? $this->category->CategoryTranslations->first()->name : null,

            'images' => $this->images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'image' => url('storage/' . $image->image),
                ];
            }),
        ];

    }
}
