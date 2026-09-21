<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JurnalArtikel;
use App\Models\Bulletin;
use App\Models\Berita;
use App\Models\Kegiatan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function stats(Request $request)
    {
        $counts=[
            'jurnal_artikel' => [
                'label' => 'Jurnal & Artikel',
                'total' => JurnalArtikel::count(),
                'publish' => JurnalArtikel::where('status', 'publish')->count(),
            ],
            'bulletin' => [
                'label' => 'Bulletin',
                'total' => Bulletin::count(),
                'publish' => Bulletin::where('status', 'publish')->count(),
            ],
            'berita' => [
                'label' => 'Berita',
                'total' => Berita::count(),
                'publish' => Berita::where('status', 'publish')->count(),
            ],
            'kegiatan' => [
                'label' => 'Kegiatan',
                'total' => Kegiatan::count(),
                'publish' => Kegiatan::where('status', 'publish')->count(),
            ],
        ];
        $recent = collect()
        ->concat(JurnalArtikel::with('user')->latest()->take(6)->get()->map(fn ($item) =>[
            'judul' => $item->judul,
            'subjudul' => $item->penulis,
            'kategori' => 'Jurnal & Artikel',
            'status' => $item->status,
            'tanggal' => $item->tanggal_terbit,
        ]))
        ->concat(Bulletin::with('user')->latest()->take(6)->get()->map(fn ($item) =>[
            'judul' => $item->judul,
            'subjudul' => $item->user->name ?? '-',
            'kategori' => 'Bulletin',
            'status' => $item->status,
            'tanggal' => $item->created_at->format('Y-m-d'),
        ]))
        ->concat(Berita::with('user')->latest()->take(6)->get()->map(fn ($item) =>[
            'judul' => $item->judul,
            'subjudul' => $item->user->name ?? '-',
            'kategori' => 'Berita',
            'status' => $item->status,
            'tanggal' => $item->tanggal_publish,
        ]))
        ->concat(Kegiatan::with('user')->latest()->take(6)->get()->map(fn ($item) =>[
            'judul' => $item->judul,
            'subjudul' => $item->user->name ?? '-',
            'kategori' => 'Kegiatan',
            'status' => $item->status,
            'tanggal' => $item->tanggal_mulai->format('Y-m-d'),
        ]))
        ->sortByDesc('tanggal')
        ->take(6)
        ->values();
        return response()->json([
            'counts' => $counts,
            'recent' => $recent,
        ]);
    }
}
