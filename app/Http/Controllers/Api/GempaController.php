<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BmkgGempaService;
use Illuminate\Http\Request;

class GempaController extends Controller
{
    public function __construct(protected BmkgGempaService $service) {}
    public function terkini()
    {
        return $this->respond(fn () => $this->service->getTerkini());
    }
    public function m5()
    {
        return $this->respond(fn () => $this->service->getListM5());
    }
    public function dirasakan()
    {
        return $this->respond(fn () => $this->service->getListDirasakan());
    }
    protected function respond(\Closure $callback)
    {
        try{
            return response()->json($callback());
        } catch (\Throwable $e){
            report ($e);
            return response()->json(['message' => 'Failed to fetch data'], 502);
        }
    }
}
