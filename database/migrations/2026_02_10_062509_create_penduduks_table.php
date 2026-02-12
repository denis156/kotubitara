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
        Schema::create('penduduks', function (Blueprint $table) {
            $table->id();
            $table->string('nik', 16)->unique();
            $table->unsignedBigInteger('kartu_keluarga_id')->nullable()->index();
            $table->foreignId('desa_id')->constrained('desas')->cascadeOnDelete();
            $table->string('nama_lengkap');
            $table->string('jenis_kelamin');
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->string('agama');
            $table->string('status_perkawinan');
            $table->string('hubungan_keluarga');
            $table->string('pekerjaan')->nullable();
            $table->string('pendidikan')->nullable();
            $table->string('nama_ayah')->nullable();
            $table->string('nama_ibu')->nullable();
            $table->string('kewarganegaraan')->default('wni');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penduduks');
    }
};
