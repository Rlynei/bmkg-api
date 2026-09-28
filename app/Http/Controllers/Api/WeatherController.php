<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BmkgWeatherService;
use App\Models\Wilayah;
use Illuminate\Http\Request;

class WeatherController extends Controller
{
    public function __construct(protected BmkgWeatherService $bmkgWeatherService) {}
    //GET/api/cuaca/{kodeAdm4}
    public function show(string $kodeAdm4)
    {
        try {
            $data = $this->bmkgWeatherService->getPrakiraanCuaca($kodeAdm4);
        } catch (\Exception $e){
            return response()->json([
                'message' => $e->getMessage(),
            ], 502);
        }
        return response()->json([
            'data' => $data,
        ]);
    }
    public function search(Request $request)
    {
        $request->validate(['q' => 'required|string|min:3']);
        $terms = preg_split('/\s+/', trim($request->q));
        $query = Wilayah::query();
        foreach ($terms as $term){
            $query->where(function ($q) use ($term) {
                $q->where('nama_kelurahan', 'like', "%$term%")
                    ->orWhere('nama_kecamatan', 'like', "%$term%")
                    ->orWhere('nama_kabupaten', 'like', "%$term%")
                    ->orWhere('nama_provinsi', 'like', "%$term%");
            });
        }
        return response()->json(
            $query->orderByRaw('nama_kelurahan LIKE ? DESC', ["{$terms[0]}%"])
                ->orderBy('nama_kelurahan')
                ->limit(15)
                ->get()
        );
    }
}
