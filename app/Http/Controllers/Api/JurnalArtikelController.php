<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreJurnalArtikelRequest;
use App\Http\Requests\UpdateJurnalArtikelRequest;
use App\Models\JurnalArtikel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class JurnalArtikelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(request $request)
    {
        $query = JurnalArtikel::query();

        // Kalau bukan lewat admin (tidak login), hanya tampilkan yang publish
        if (! $request->user()) {
            $query->where('status', 'publish');
        }
        $data = $query->latest('tanggal_terbit')->paginate($request->get('per_page', 10));
        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    // POST /api/admin/jurnal-artikel — admin/editor only
    public function store(StoreJurnalArtikelRequest $request)
    {
        $validated = $request->validated();

        $validated['slug'] = Str::slug($validated['judul']) . '-' . uniqid();
        $validated['user_id'] = $request->user()->id;
        $validated['file_pdf'] = $request->file('file_pdf')->store('jurnal/pdf', 'public');

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('jurnal/thumbnail', 'public');
        }
        $item = JurnalArtikel::create($validated);
        return response()->json($item, 201);
    }

    /**
     * Display the specified resource.
     */
    // GET /api/jurnal-artikel/{slug} — publik
    public function show(string $slug)
    {
        $item = JurnalArtikel::where('slug', $slug)->firstOrFail();
        $item->increment('dilihat');
        return response()->json($item);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateJurnalArtikelRequest $request, JurnalArtikel $jurnalArtikel)
    {
        $validated = $request->validated();

        if (isset($validated['judul'])) {
            $validated['slug'] = Str::slug($validated['judul']) . '-' . $jurnalArtikel->id;
        }
        if ($request->hasFile('file_pdf')) {
            // Hapus file lama
            Storage::disk('public')->delete($jurnalArtikel->file_pdf);
            $validated['file_pdf'] = $request->file('file_pdf')->store('jurnal/pdf', 'public');
        }
        if ($request->hasFile('thumbnail')){
            if ($jurnalArtikel->thumbnail) {
                Storage::disk('public')->delete($jurnalArtikel->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')->store('jurnal/thumbnail', 'public');
        }
        $jurnalArtikel->update($validated);
        return response()->json($jurnalArtikel);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JurnalArtikel $jurnalArtikel)
    {
        Storage::disk('public')->delete($jurnalArtikel->file_pdf);
        if ($jurnalArtikel->thumbnail) {
            Storage::disk('public')->delete($jurnalArtikel->thumbnail);
        }
        $jurnalArtikel->delete();
        return response()->json(['message' => 'Jurnal Artikel sudah dihapus']);
    }
}
