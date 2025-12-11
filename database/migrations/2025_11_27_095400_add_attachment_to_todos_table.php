<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom path lampiran pada tabel todos.
     */
    public function up(): void
    {
        Schema::table('todos', function (Blueprint $table) {
            $table->string('attachment_path')->nullable()->after('category');
        });
    }

    /**
     * Rollback penambahan kolom lampiran.
     */
    public function down(): void
    {
        Schema::table('todos', function (Blueprint $table) {
            $table->dropColumn('attachment_path');
        });
    }
};
