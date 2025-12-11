<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom file_path untuk menyimpan lokasi file pada storage publik.
     */
    public function up(): void
    {
        Schema::table('todos', function (Blueprint $table) {
            $table->string('file_path')->nullable()->after('attachment_path');
        });
    }

    /**
     * Rollback kolom file_path.
     */
    public function down(): void
    {
        Schema::table('todos', function (Blueprint $table) {
            $table->dropColumn('file_path');
        });
    }
};
