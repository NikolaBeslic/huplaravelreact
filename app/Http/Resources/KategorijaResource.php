<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class KategorijaResource extends JsonResource
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
            'kategorijaid' => $this->kategorijaid,
            'naziv_kategorije' => $this->naziv_kategorije,
            'kategorija_slug' => $this->kategorija_slug,
            'tekstovi' => $this->tekstovi()->paginate(8)
        ];
    }
}
