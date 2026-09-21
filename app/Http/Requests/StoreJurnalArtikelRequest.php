<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;


class StoreJurnalArtikelRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // otorisasi role sudah dihandle di middleware route
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'judul' => 'required|string|max:255',
            'penulis' => 'required|string|max:255',
            'abstrak' => 'nullable|string',
            'file_pdf' => 'required|file|mimes:pdf|max:10240', // max 10MB
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'tanggal_terbit' => 'required|date',
            'status' => 'required|in:draft,publish',
        ];
    }
}
