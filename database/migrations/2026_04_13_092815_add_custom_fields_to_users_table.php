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
        Schema::table('users', function (Blueprint $table) {
            // 1. Rename the default 'name' to 'full_name' or add it
            if (Schema::hasColumn('users', 'name')) {
                $table->renameColumn('name', 'full_name');
            } else {
                $table->string('full_name')->after('id');
            }

            // 2. Add the other missing columns
            $table->foreignId('department_id')->nullable()->after('full_name')->constrained()->onDelete('set null');
            $table->string('username')->unique()->index()->after('full_name');
            $table->string('role_level')->default('employee')->index(); // admin, manager, employee
            $table->string('preferred_lang')->default('tk'); // tk, ru, en
            $table->boolean('is_active')->default(true);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['full_name', 'department_id', 'username', 'role_level', 'preferred_lang', 'is_active']);
        });
    }
};
