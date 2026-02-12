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
        Schema::create('aparat_desas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('desa_id')->constrained('desas')->cascadeOnDelete();
            $table->string('nama_lengkap');
            $table->string('nip')->nullable()->unique();
            $table->enum('jabatan', [
                'kepala-desa',
                'sekretaris-desa',
                'kaur-tata-usaha-umum',
                'kaur-keuangan',
                'kaur-perencanaan',
                'kasi-pemerintahan',
                'kasi-kesejahteraan',
                'kasi-pelayanan',
                'kepala-dusun',
            ]);
            $table->string('nama_dusun')->nullable()->comment('Untuk kepala dusun');
            $table->string('telepon')->nullable();
            $table->string('email')->nullable();
            $table->text('alamat')->nullable();
            $table->date('tanggal_mulai_jabatan')->nullable();
            $table->date('tanggal_selesai_jabatan')->nullable();
            $table->enum('status', ['aktif', 'non-aktif'])->default('aktif');
            $table->string('foto')->nullable()->comment('Foto aparat desa');
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
        Schema::dropIfExists('aparat_desas');
    }
};
