<?php

namespace App\Http\Resources;

use DateTime;
use Illuminate\Http\Resources\Json\JsonResource;

class PredstavaResource extends JsonResource
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
            'opis' => $this->opis,
            'uloge' => $this->uloge,
            'autor' => $this->autor,
            'reditelj' => $this->reditelj,
            'premijera' => date('d. M. Y.', strtotime($this->premijera)),
            'prosecnaOcena' => $this->prosecnaOcena,
            'pozorista' => PozoristeResource::collection($this->pozorista),
            'tekstovi' => TekstResource::collection($this->tekstovi),
            'zanrovi' => ZanrResource::collection($this->zanrovi),
            'igranja' => IgranjeResource::collection($this->igranja),
            'komentari' => KomentarResource::collection($this->komentari)

        ];
    }
}
