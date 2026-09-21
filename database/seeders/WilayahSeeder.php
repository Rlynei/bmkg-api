<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Wilayah;
use Illuminate\Database\Seeder;

class WilayahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Wilayah::create([
            'kode_adm4' => '31.71.03.1001',
            'nama_kelurahan' => 'Cipinang Muara',
            'nama_kecamatan' => 'Jatinegara',
            'nama_kabupaten' => 'Kota Jakarta Timur',
            'nama_provinsi' => 'DKI Jakarta',
        ]);
        Wilayah::create([
            'kode_adm4' => '31.71.03.1002',
            'nama_kelurahan' => 'Rawa Bunga',
            'nama_kecamatan' => 'Jatinegara',
            'nama_kabupaten' => 'Kota Jakarta Timur',
            'nama_provinsi' => 'DKI Jakarta',
        ]);
    }
}
