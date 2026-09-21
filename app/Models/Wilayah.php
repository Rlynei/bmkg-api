<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wilayah extends Model
{
    protected $fillable = ['kode_adm4', 'nama_kelurahan', 'nama_kecamatan', 'nama_kabupaten', 'nama_provinsi'];
}
