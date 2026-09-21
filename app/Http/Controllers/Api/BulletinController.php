<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBulletinRequest;
use App\Http\Requests\UpdateBulletinRequest;
use App\Models\Bulletin;
use Illuminate\Http\Request;
use Illuminate\support\str;
use illuminate\Support\Facades\Storage;

class BulletinController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(request $request)
    {
        $query = Bulletin::query();
        if (! $request->user()) {
            $query->where('status', 'publish');
        }
        if ($request->has('jenis')) {
            $query->where('jenis', $request->jenis);
        }
        return response()->json($query->latest()->paginate($request->get('per_page', 10)));
        //return response()->json($query->latest()->paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBulletinRequest $request)
    {
        $validated = $request->validated();
        $validated['slug'] = Str::slug($validated['judul']). '-' . uniqid();
        $validated['user_id'] = $request->user()->id;
        $validated['file_pdf'] = $request->file('file_pdf')->store('bulletin/pdf', 'public');
        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('bulletin/thumbnail', 'public');
        }
        return response()->json(Bulletin::create($validated), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        return response()->json(Bulletin::where('slug', $slug)->firstOrFail());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBulletinRequest $request, Bulletin $bulletin)
    {
        $validated = $request->validated();
        if (isset($validated['judul'])) {
            $validated['slug'] = Str::slug($validated['judul']). '-' . $bulletin->id;
        }
        if ($request->hasFile('file_pdf')) {
            Storage::disk('public')->delete($bulletin->file_pdf);
            $validated['file_pdf'] = $request->file('file_pdf')->store('bulletin/pdf', 'public');
        }
        if ($request->hasFile('thumbnail')){
            if ($bulletin->thumbnail) Storage::disk('public')->delete($bulletin->thumbnail);
            $validated['thumbnail'] = $request->file('thumbnail')->store('bulletin/thumbnail', 'public');
        }
        
        $bulletin->update($validated);
        return response()->json($bulletin);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bulletin $bulletin)
    {
        Storage::disk('public')->delete($bulletin->file_pdf);
        if ($bulletin->thumbnail) {
            Storage::disk('public')->delete($bulletin->thumbnail);  
        }
        $bulletin->delete();
        return response()->json([
            'message' => 'Bulletin deleted successfully',
        ]);
    }
}
