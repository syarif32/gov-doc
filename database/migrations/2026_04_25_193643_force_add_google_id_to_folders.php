<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('folders', function (Blueprint $table) {
            // Cek dulu biar gak error kalau ternyata kolomnya udah ada
            if (!Schema::hasColumn('folders', 'google_folder_id')) {
                $table->string('google_folder_id')->nullable()->after('name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('folders', function (Blueprint $table) {
            $table->dropColumn('google_folder_id');
        });
    }
};