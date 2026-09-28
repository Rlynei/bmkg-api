<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ImportWilayah extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wilayah:import {file=database/data/wilayah_2023.sql}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Impor kode wilayah desa/kelurahan (cahyadsn/wilayah) ke tabel wilayahs';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $path = base_path($this->argument('file'));
        if (! file_exists($path)) {
            $this->error("File not found: $path");
            return self::FAILURE;
        }
        $this->info('1/3 Memuat file SQL ke tabel sementara "wilayah"...');
        $statements = preg_split('/;\s*[\r\n]+/', file_get_contents($path));
        $bar = $this->output->createProgressBar(count($statements));

        foreach ($statements as $statement) {
            $clean = trim(preg_replace('/^--.*$/m', '', $statement));
            if ($clean !== '') {
                DB::statement($clean);
            }
            $bar->advance();
        }
        $bar->finish();
        $this->newLine();
        $this->info('2/3 Menyusun data desa/kelurahan ke tabel wilayahs...');
        DB::statement("
        INSERT INTO wilayahs (kode_adm4, nama_kelurahan, nama_kecamatan, nama_kabupaten, nama_provinsi, created_at, updated_at)
        SELECT d.kode, d.nama, k.nama, c.nama, p.nama, NOW(), NOW()
        FROM wilayah d
        JOIN wilayah k ON k.kode = LEFT(d.kode, 8)
        JOIN wilayah c ON c.kode = LEFT(d.kode, 5)
        JOIN wilayah p ON p.kode = LEFT(d.kode, 2)
        WHERE CHAR_LENGTH(d.kode) = 13
        ON DUPLICATE KEY UPDATE
            nama_kelurahan = VALUES(nama_kelurahan),
            nama_kecamatan = VALUES(nama_kecamatan),
            nama_kabupaten = VALUES(nama_kabupaten),
            nama_provinsi = VALUES(nama_provinsi),
            updated_at = NOW()
        ");
        $this->info('3/3 Membersihkan tabel sementara...');
        Schema::dropIfExists('wilayah');

        $this->info('Selesai. Total baris di wilayahs: ' . number_format(DB::table('wilayahs')->count()));
        return self::SUCCESS;
    }
}
