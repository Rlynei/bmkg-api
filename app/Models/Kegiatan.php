<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kegiatan extends Model
{
    protected $fillable = ['user_id', 'judul', 'slug', 'deskripsi', 'lokasi', 'tanggal_mulai', 'tanggal_selesai', 'thumbnail', 'status'];
    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];
    public function user()
    {
    return $this->belongsTo(User::class);
    }
}
