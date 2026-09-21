<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JurnalArtikel extends Model
{
    protected $fillable = [
    'user_id', 'judul', 'slug', 'penulis', 'abstrak',
    'file_pdf', 'thumbnail', 'tanggal_terbit', 'status', 'dilihat',
    ];
    protected $casts = [
        'tanggal_terbit' => 'date',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
