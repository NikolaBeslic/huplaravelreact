<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Date;

class TekstResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // dd($this);
        return [
            'tekstid' => $this->tekstid,
            'naslov' => $this->naslov,
            'uvod' => $this->uvod,
            'slug' => $this->slug,
            'sadrzaj' => $this->sadrzaj,
            'tekst_photo' => $this->tekst_photo,
            'na_slajderu' => $this->na_slajderu,
            'created_at' => $this->created_at->format('d.M.Y'),
            'published_at' =>  optional($this->published_at)->format('d.M.Y'),
            'kategorija' => $this->kategorija,
            'autori' => AutorResource::collection($this->autori)
        ];
    }
}
