<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class KomentarResource extends JsonResource
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
            'komentarid' => $this->komentarid,
            'tekst_komentara' => $this->tekst_komentara,
            'created_at' => $this->created_at,
            'korisnik' => new KorisnikResource($this->korisnik)
        ];
    }
}
