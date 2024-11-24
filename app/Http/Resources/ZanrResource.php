<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ZanrResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'zanrid' => $this->zanrid,
            'naziv_zanra' => $this->naziv_zanra,
            'zanr_slug' => $this->zanr_slug,
            'zanr_boja' => $this->zanr_boja
        ];
    }
}
