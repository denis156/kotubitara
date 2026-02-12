<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Alter enum untuk menambahkan 'saudara-kandung'
        DB::statement("ALTER TABLE penduduks MODIFY COLUMN hubungan_keluarga ENUM(
            'kepala-keluarga',
            'suami',
            'istri',
            'anak',
            'menantu',
            'cucu',
            'orang-tua',
            'mertua',
            'saudara-kandung',
            'famili-lain',
            'pembantu',
            'lainnya'
        )");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan ke enum tanpa 'saudara-kandung'
        DB::statement("ALTER TABLE penduduks MODIFY COLUMN hubungan_keluarga ENUM(
            'kepala-keluarga',
            'suami',
            'istri',
            'anak',
            'menantu',
            'cucu',
            'orang-tua',
            'mertua',
            'famili-lain',
            'pembantu',
            'lainnya'
        )");
    }
};
