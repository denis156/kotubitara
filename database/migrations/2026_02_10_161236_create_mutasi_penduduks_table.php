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
        Schema::create('mutasi_penduduks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penduduk_id')->constrained('penduduks')->cascadeOnDelete();
            $table->foreignId('desa_id')->constrained('desas')->cascadeOnDelete();
            $table->string('jenis_mutasi');
            $table->date('tanggal_mutasi');
            $table->string('alamat_asal')->nullable();
            $table->string('alamat_tujuan')->nullable();
            $table->string('alasan')->nullable();
            $table->string('no_surat_pindah')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mutasi_penduduks');
    }
};
