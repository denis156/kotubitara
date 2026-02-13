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
        Schema::create('aparat_kecamatans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kecamatan_id')->constrained('kecamatans')->cascadeOnDelete();
            $table->string('nama_lengkap');
            $table->string('nip')->nullable()->unique();
            $table->string('jabatan');
            $table->string('telepon')->nullable();
            $table->string('email')->nullable();
            $table->text('alamat')->nullable();
            $table->date('tanggal_mulai_jabatan')->nullable();
            $table->date('tanggal_selesai_jabatan')->nullable();
            $table->string('status')->default('aktif');
            $table->string('foto')->nullable()->comment('Foto aparat kecamatan');
            $table->text('ttd_digital')->nullable()->comment('Tanda tangan digital dari autograph');
            $table->string('foto_ttd')->nullable()->comment('Upload foto tanda tangan');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aparat_kecamatans');
    }
};
