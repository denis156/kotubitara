<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kematians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penduduk_id')->constrained('penduduks')->cascadeOnDelete();
            $table->foreignId('desa_id')->constrained('desas')->cascadeOnDelete();

            // Data Kematian
            $table->date('tanggal_meninggal');
            $table->time('waktu_meninggal')->nullable();
            $table->string('tempat_meninggal')->nullable();
            $table->string('sebab_kematian')->nullable();
            $table->string('tempat_pemakaman')->nullable();
            $table->date('tanggal_pemakaman')->nullable();

            // Data Pelapor
            $table->string('nama_pelapor');
            $table->string('nik_pelapor', 16)->nullable();
            $table->string('hubungan_pelapor');
            $table->text('alamat_pelapor')->nullable();
            $table->string('telepon_pelapor')->nullable();

            // Dokumen Pendukung
            $table->string('foto_surat_rs')->nullable()->comment('Upload foto surat keterangan dari RS/dokter');

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
        Schema::dropIfExists('kematians');
    }
};
