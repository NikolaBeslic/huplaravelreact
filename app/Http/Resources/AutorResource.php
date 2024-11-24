<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AutorResource extends JsonResource
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
            'autorid' => $this->autorid,
            'ime_autora' => $this->ime_autora,
            'autor_slug' => $this->autor_slug,
            'autor_photo' => $this->url_slike,
            'biografija' => $this->biografija,
            'pozicija' => $this->pozicija
        ];
    }
}
