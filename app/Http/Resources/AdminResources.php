<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminResources extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */

    public function toArray($request)
    {

        return [
            'id' => $this->id,
            'accountId'=>$this->account->id,
            'userId' => $this->id,
            'fullName' => $this->account->fullName,
            'email' => $this->email,
            'profileImage' => $this->account->profileImage,
        ];
    }



}
