<?php


namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CycleResource extends JsonResource
{


    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */

    protected $price;
    public function toArray($request)
    {

        return [
            'id' => $this->id,
            'name' => $this->name,
            'status' => $this->status ? 1 : 0,
            'days' => is_string($this->days) ? json_decode($this->days, true) : $this->days,
            'days_count'=> count(is_string($this->days) ? json_decode($this->days, true) : $this->days),

        ];
    }
}
