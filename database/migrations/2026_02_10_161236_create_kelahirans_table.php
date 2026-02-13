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
        Schema::create('kelahirans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('desa_id')->constrained('desas')->cascadeOnDelete();

            // Data Bayi
            $table->string('nama_bayi');
            $table->string('nik_bayi', 16)->nullable()->unique();
            $table->string('jenis_kelamin');
            $table->date('tanggal_lahir');
            $table->time('waktu_lahir')->nullable();
            $table->string('tempat_lahir');
            $table->decimal('berat_lahir', 5, 2)->nullable()->comment('dalam kilogram');
            $table->decimal('panjang_lahir', 5, 2)->nullable()->comment('dalam centimeter');

            // Data Orang Tua
            $table->foreignId('ayah_id')->nullable()->constrained('penduduks')->nullOnDelete();
            $table->foreignId('ibu_id')->nullable()->constrained('penduduks')->nullOnDelete();

            // Data Pelapor
            $table->string('nama_pelapor');
            $table->string('nik_pelapor', 16)->nullable();
            $table->string('hubungan_pelapor');
            $table->text('alamat_pelapor')->nullable();
            $table->string('telepon_pelapor')->nullable();

            // Dokumen Pendukung
            $table->string('foto_surat_rs')->nullable()->comment('Surat keterangan lahir dari RS/Bidan/Puskesmas');

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
        Schema::dropIfExists('kelahirans');
    }
};
