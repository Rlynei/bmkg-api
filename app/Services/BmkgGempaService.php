<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class BmkgGempaService
{
    protected string $baseUrl = 'https://data.bmkg.go.id/DataMKG/TEWS';

    public function getTerkini(): array
    {
        return Cache::remember('bmkg_gempa_terkini', now()->addMinutes(2), function () {
            $data = $this->fetch('/autogempa.json');
            $gempa = $data['Infogempa']['gempa'] ?? $data['gempa'] ?? null;

            if (! $gempa) {
                throw new \RuntimeException('Format data gempa terbaru tidak dikenali');
            }

            return $this->normalize($gempa);
        });
    }

    public function getListM5(): array
    {
        return Cache::remember('bmkg_gempa_m5', now()->addMinutes(5), function () {
            $data = $this->fetch('/gempaterkini.json');
            $list = $data['Infogempa']['gempa'] ?? $data['gempa'] ?? [];

            return array_map(fn ($g) => $this->normalize($g), $list);
        });
    }

    public function getListDirasakan(): array
    {
        return Cache::remember('bmkg_gempa_dirasakan', now()->addMinutes(5), function () {
            $data = $this->fetch('/gempadirasakan.json');
            $list = $data['Infogempa']['gempa'] ?? $data['gempa'] ?? [];

            return array_map(fn ($g) => $this->normalize($g), $list);
        });
    }

    protected function normalize(array $g): array
    {
        $coords = null;
        $rawCoords = $g['point']['coordinates'] ?? $g['Coordinates'] ?? null;
        if (is_string($rawCoords)) {
            $parts = array_map('trim', explode(',', $rawCoords));
            if (count($parts) === 2 && is_numeric($parts[0]) && is_numeric($parts[1])) {
                $coords = ['lat' => (float) $parts[0], 'lon' => (float) $parts[1]];
            }
        }

        $shakemap = $g['Shakemap'] ?? null;

        return [
            'tanggal' => $g['Tanggal'] ?? null,
            'jam' => $g['Jam'] ?? null,
            'datetime' => $g['DateTime'] ?? null,
            'magnitude' => isset($g['Magnitude']) ? (float) $g['Magnitude'] : null,
            'kedalaman' => $g['Kedalaman'] ?? null,
            'lintang' => $g['Lintang'] ?? null,
            'bujur' => $g['Bujur'] ?? null,
            'coordinates' => $coords,
            'wilayah' => $g['Wilayah'] ?? null,
            'potensi' => $g['Potensi'] ?? null,
            'dirasakan' => $g['Dirasakan'] ?? null,
            'shakemap_url' => $shakemap ? "https://static.bmkg.go.id/{$shakemap}" : null,
        ];
    }

    protected function fetch(string $path): array
    {
        $response = Http::timeout(10)->get("{$this->baseUrl}{$path}");

        if ($response->failed()) {
            throw new \RuntimeException('Gagal mengambil data dari BMKG');
        }

        $data = $response->json();

        if (! is_array($data)) {
            throw new \RuntimeException('Format data BMKG tidak valid');
        }

        return $data;
    }
}