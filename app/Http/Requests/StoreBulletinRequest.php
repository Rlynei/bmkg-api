<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBulletinRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool{return true;
    }
    //{
        //return false;
    //}

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
        'judul' => 'required|string|max:255',
        'jenis' => 'required|in:cuaca,iklim,geofisika,maritim',
        'periode' => 'required|string|max:100',
        'file_pdf' => 'required|file|mimes:pdf|max:10240',
        'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }
}
