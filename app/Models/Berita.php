<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Berita extends Model
{
    protected $fillable = ['user_id', 'judul', 'slug', 'isi', 'thumbnail', 'kategori', 'tanggal_publish', 'status', 'dilihat'];
    protected $casts = [
        'tanggal_publish' => 'date',
    ];
    public function user()
    {
    return $this->belongsTo(User::class);
    }
}
