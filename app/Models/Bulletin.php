<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bulletin extends Model
{
    protected $fillable = ['user_id', 'judul', 'slug', 'jenis', 'periode', 'file_pdf', 'thumbnail', 'status'];
    protected $casts = [
        'periode' => 'date',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

