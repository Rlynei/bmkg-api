<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jurnal_artikels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('judul');
            $table->string('slug')->unique();
            $table->string('penulis');
            $table->text('abstrak')->nullable();
            $table->string('file_pdf'); // path file
            $table->string('thumbnail')->nullable();
            $table->date('tanggal_terbit');
            $table->enum('status', ['draft', 'publish'])->default('draft');
            $table->unsignedInteger('dilihat')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jurnal_artikels');
    }
};
