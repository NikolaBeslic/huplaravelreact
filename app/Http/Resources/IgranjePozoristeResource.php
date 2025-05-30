<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class IgranjePozoristeResource extends JsonResource
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
            'pozoristeid' => $this->pozoristeid,
            'naziv_pozorista' => $this->naziv_pozorista,
            'pozoriste_slug' => $this->pozoriste_slug,
            'skraceni_naziv' => $this->skraceni_naziv,
        ];
    }
}
