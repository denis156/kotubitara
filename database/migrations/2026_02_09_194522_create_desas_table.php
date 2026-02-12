<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('desas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_desa', 100);
            $table->string('slug', 100)->unique();
            $table->string('kode_desa', 20)->unique();
            $table->string('kode_provinsi', 2);
            $table->string('nama_provinsi');
            $table->string('kode_kabupaten', 4);
            $table->string('nama_kabupaten');
            $table->string('kode_kecamatan', 7);
            $table->string('nama_kecamatan');
            $table->string('kecamatan', 100);
            $table->text('alamat')->nullable();
            $table->string('telepon', 20)->nullable();
            $table->string('email')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('desas');
    }
};
