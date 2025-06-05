<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class ZipCodeService
{
    public function lookup(string $cep): ?array
    {
        return Cache::remember("zipcode:$cep", now()->addDay(), function () use ($cep) {
            $url = rtrim(Config::get('services.viacep.url'), '/');
            $response = Http::get("{$url}/{$cep}/json/");

            if ($response->failed() || $response->json('erro')) {
                return null;
            }

            $data = $response->json();

            return [
                'cep' => $data['cep'] ?? null,
                'street' => $data['logradouro'] ?? null,
                'neighborhood' => $data['bairro'] ?? null,
                'city' => $data['localidade'] ?? null,
                'state' => $data['uf'] ?? null,
            ];
        });
    }
}
