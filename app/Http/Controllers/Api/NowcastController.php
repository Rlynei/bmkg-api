<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\BmkgNowcastService;

class NowcastController extends Controller
{
    public function __construct(protected BmkgNowcastService $service) {}

    public function index()
    {
        try{
            return response()->json($this->service->getAlerts());
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Failed to fetch nowcast alerts'], 502);
        }
    }
    public function show(string $kode){
        // Kode ikut membentuk URL yang diambil server, jadi dibatasi ketat
        if (! preg_match('/^[A-Za-z0-9]{6,30}$/', $kode)){
            return response()->json(['error' => 'Invalid kode format'], 422);
        }
        try{
            return response()->json($this->service->getAlertDetail($kode));
        } catch (\DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['message' => 'Failed to fetch nowcast alert detail'], 502);
        }
    }
    
}
