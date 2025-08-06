<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class IgranjeResource extends JsonResource
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
            'seigraid' => $this->seigraid,
            'datum' => $this->datum,
            'vreme' => $this->vreme,
            'scena' => new ScenaResource($this->scena),
            'predstava' => new IgranjePredstavaResource($this->predstava),
            'pozoriste' => new IgranjePozoristeResource($this->pozoriste),
        ];
    }
}
