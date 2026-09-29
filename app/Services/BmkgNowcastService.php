<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class BmkgNowcastService
{
    protected string $baseUrl = 'https://www.bmkg.go.id/alerts/nowcast/id';

    public function getAlerts(): array
    {
        return Cache::remember('bmkg_nowcast_list', now()->addMinutes(2), function () {
            $xml = $this->fetchXml("{$this->baseUrl}/rss.xml");

            $items = [];
            foreach ($xml->channel->item ?? [] as $item) {
                $link = (string) $item->link;

                // Kode detail diambil dari link, mis. .../CJT20251012004_alert.xml
                if (! preg_match('/\/([A-Za-z0-9]+)_alert\.xml$/', $link, $m)) {
                    continue;
                }

                // Judul berformat "<jenis peringatan> di <provinsi>"
                $title = trim((string) $item->title);
                $event = $title;
                $provinsi = '';
                if (preg_match('/^(.*) di (.+)$/u', $title, $t)) {
                    $event = $t[1];
                    $provinsi = $t[2];
                }

                $pub = (string) $item->pubDate;

                $items[] = [
                    'kode' => $m[1],
                    'title' => $title,
                    'event' => $event,
                    'provinsi' => $provinsi,
                    'description' => trim((string) $item->description),
                    'pub_date' => $pub !== '' ? Carbon::parse($pub)->toIso8601String() : null,
                ];
            }

            return $items;
        });
    }

    public function getAlertDetail(string $code): array
    {
        return Cache::remember("bmkg_nowcast_detail_{$code}", now()->addMinutes(5), function () use ($code) {
            $xml = $this->fetchXml("{$this->baseUrl}/{$code}_alert.xml");

            $capNs = $xml->getNamespaces(true)[''] ?? 'urn:oasis:names:tc:emergency:cap:1.2';
            $info = $xml->children($capNs)->info->children($capNs);

            if ((string) $info->headline === '' && (string) $info->event === '') {
                throw new \DomainException('Data peringatan tidak ditemukan');
            }

            $areas = [];
            foreach ($info->area as $area) {
                $a = $area->children($capNs);

                $polygons = [];
                foreach ($a->polygon as $poly) {
                    $coords = [];
                    foreach (preg_split('/\s+/', trim((string) $poly)) as $pair) {
                        $ll = explode(',', $pair);
                        if (count($ll) === 2 && is_numeric($ll[0]) && is_numeric($ll[1])) {
                            $coords[] = [(float) $ll[0], (float) $ll[1]]; // [lat, lon]
                        }
                    }
                    if ($coords) {
                        $polygons[] = $coords;
                    }
                }

                $areas[] = [
                    'area_desc' => (string) $a->areaDesc,
                    'polygons' => $polygons,
                ];
            }

            $web = (string) $info->web;

            return [
                'kode' => $code,
                'headline' => (string) $info->headline,
                'event' => (string) $info->event,
                'urgency' => (string) $info->urgency,
                'severity' => (string) $info->severity,
                'certainty' => (string) $info->certainty,
                'effective' => (string) $info->effective,
                'expires' => (string) $info->expires,
                'sender_name' => (string) $info->senderName,
                'description' => trim((string) $info->description),
                'web' => $web !== '' ? $web : null,
                'areas' => $areas,
            ];
        });
    }

    protected function fetchXml(string $url): \SimpleXMLElement
    {
        $response = Http::timeout(10)->withUserAgent('BMKG-Web/1.0')->get($url);

        if ($response->status() === 404) {
            throw new \DomainException('Data tidak ditemukan');
        }
        if ($response->failed()) {
            throw new \RuntimeException('Gagal mengambil data dari BMKG');
        }

        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($response->body());
        libxml_clear_errors();

        if ($xml === false) {
            throw new \RuntimeException('Format data BMKG tidak valid');
        }

        return $xml;
    }
}