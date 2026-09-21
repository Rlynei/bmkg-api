<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class BmkgWeatherService
{
    protected string $baseUrl = 'https://api.bmkg.go.id/publik/prakiraan-cuaca';

    public function getPrakiraanCuaca(string $kodeAdm4): array
    {
        $cacheKey = "bmkg_cuaca_{$kodeAdm4}";

        // Data BMKG update 2x sehari, jadi cache 3 jam sudah aman dan hemat request
        return Cache::remember($cacheKey, now()->addHours(3), function () use ($kodeAdm4) {
            $response = Http::timeout(10)->get($this->baseUrl, [
                'adm4' => $kodeAdm4,
            ]);

            if ($response->failed()) {
                throw new \Exception('Gagal mengambil data cuaca dari BMKG');
            }

            return $response->json();
        });
    }
}