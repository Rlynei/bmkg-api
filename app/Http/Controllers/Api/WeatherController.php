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
        $wilayah = Wilayah::where('nama_kelurahan', 'like', "%{$request->q}%")
        ->orWhere('nama_kecamatan', 'like', "%{$request->q}%")
        ->limit(10)
        ->get();
        return response()->json($wilayah);
    }
}
