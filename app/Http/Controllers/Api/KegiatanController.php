<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreKegiatanRequest;
use App\Http\Requests\UpdateKegiatanRequest;
use App\Models\Kegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class KegiatanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = kegiatan::query();
        if (! $request->user()) {
            $query->where('status', 'publish');
        }
        return response()->json($query->orderBy('tanggal_mulai', 'desc')->paginate($request->get('per_page', 10))); 
        //return response()->json($query->orderBy('tanggal_mulai', 'desc')->paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreKegiatanRequest $request)
    {
        $validated = $request->validated();
        $validated['slug'] = Str::slug($validated['judul']) . '-' . uniqid();
        $validated['user_id'] = $request->user()->id;
        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('kegiatan/thumbnail', 'public');
        }
        return response()->json(Kegiatan::create($validated), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        return response()->json(Kegiatan::where('slug', $slug)->firstOrFail());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateKegiatanRequest $request, Kegiatan $kegiatan)
    {
        $validated = $request->validated();
        if (isset($validated['judul'])) {
            $validated['slug'] = Str::slug($validated['judul']) . '-' . $kegiatan->id;
        }
        if ($request->hasFile('thumbnail')) {
            if ($kegiatan->thumbnail) Storage::disk('public')->delete($kegiatan->thumbnail);
            $validated['thumbnail'] = $request->file('thumbnail')->store('kegiatan/thumbnail', 'public');
        }
        $kegiatan->update($validated);
        return response()->json($kegiatan);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kegiatan $kegiatan)
    {
        if ($kegiatan->thumbnail) Storage::disk('public')->delete($kegiatan->thumbnail);
        $kegiatan->delete();
        return response()->json([
            'message' => 'Kegiatan deleted successfully',
        ]);
    }
}
