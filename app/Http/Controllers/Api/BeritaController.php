<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBeritaRequest;
use App\Http\Requests\UpdateBeritaRequest;
use App\Models\Berita;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class BeritaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Berita::query();
        if (! $request->user()) {
            $query->where('status', 'publish');
        }
        if ($request->has('kategori')) {
            $query->where('kategori', $request->kategori);
        }
        return response()->json($query->latest('tanggal_publish')->paginate($request->get('per_page', 10)));
        //return response()->json($query->latest('tanggal_publish')->paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBeritaRequest $request)
    {
        $validated = $request->validated();
        $validated['slug'] = Str::slug($validated['judul']) . '-' . uniqid();
        $validated['user_id'] = $request->user()->id;
        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('berita/thumbnail', 'public');
        }
        return response()->json(Berita::create($validated), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        $item = Berita::where('slug', $slug)->firstOrFail();
        $item->increment('dilihat');
        return response()->json($item);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBeritaRequest $request, Berita $berita)
    {
        $validated = $request->validated();
        if (isset($validated['judul'])) {
            $validated['slug'] = Str::slug($validated['judul']) . '-' . $berita->id;
        }
        if ($request->hasFile('thumbnail')) {
            if ($berita->thumbnail) Storage::disk('public')->delete($berita->thumbnail);
            $validated['thumbnail'] = $request->file('thumbnail')->store('berita/thumbnail', 'public');
        }
        $berita->update($validated);
        return response()->json($berita);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Berita $berita)
    {
        if ($berita->thumbnail) Storage::disk('public')->delete($berita->thumbnail);
        $berita->delete();
        return response()->json([
            'message' => 'Berita deleted successfully',
        ]);
    }
}
