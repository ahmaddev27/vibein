<?php

namespace App\Http\Resources\dashboard;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BrandResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);
        $data['image'] = $this->image ? url('storage') . '/' . $this->image : null;
        $data['showStatus'] = $this->showStatus ? 1 : 0;

        return $data;

    }

}
