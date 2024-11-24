<?php

namespace App\Http\Resources;

use DateTime;
use Illuminate\Http\Resources\Json\JsonResource;

class IgranjePredstavaResource extends JsonResource
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
            'predstavaid' => $this->predstavaid,
            'naziv_predstave' => $this->naziv_predstave,
            'predstava_slug' => $this->predstava_slug,
            'plakat' => $this->plakat,
            'prosecnaOcena' => $this->prosecnaOcena,
            //'pozorista' => IgranjePozoristeResource::collection($this->pozorista),
            'zanrovi' => ZanrResource::collection($this->zanrovi),
        ];
    }
}
