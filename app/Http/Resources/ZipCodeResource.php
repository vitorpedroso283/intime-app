<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ZipCodeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'cep' => $this['cep'],
            'street' => $this['street'],
            'neighborhood' => $this['neighborhood'],
            'city' => $this['city'],
            'state' => $this['state'],
        ];
    }
}
