<?php

declare(strict_types=1);

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
        Schema::create('surat_keterangans', function (Blueprint $table) {
            $table->id();

            // Core Data
            $table->foreignId('desa_id')->constrained()->cascadeOnDelete();
            $table->foreignId('penduduk_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('kematian_id')->nullable()->constrained('kematians')->nullOnDelete();
            $table->string('jenis_surat'); // domisili, usaha, tidak-mampu, dll
            $table->string('keperluan')->nullable();

            // Specific Data per Jenis Surat (JSON) - semua data fleksibel ada di sini
            $table->json('data_domisili')->nullable();
            $table->json('data_usaha')->nullable();
            $table->json('data_ekonomi')->nullable();
            $table->json('data_pernikahan')->nullable();
            $table->json('data_ahli_waris')->nullable();
            $table->json('data_kematian')->nullable();
            $table->json('data_tambahan')->nullable(); // untuk TTD, pelapor, dokumen pendukung, dll

            // Surat
            $table->string('no_surat')->unique();
            $table->date('tanggal_surat');
            $table->foreignId('kepala_desa_id')->nullable()->constrained('aparat_desas')->nullOnDelete();
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
        Schema::dropIfExists('surat_keterangans');
    }
};
