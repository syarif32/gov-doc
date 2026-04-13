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
        Schema::create('folders', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->foreignId('parent_id')->nullable()->constrained('folders')->onDelete('cascade');
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('folder_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('owner_id')->constrained('users');
            $table->string('title')->index();
            $table->text('description')->nullable();
            $table->string('file_path');
            $table->string('extension', 10); // pdf, docx
            $table->integer('file_size'); // in bytes
            $table->integer('version')->default(1);
            $table->timestamps();
            $table->index(['title', 'created_at']); // Composite index for fast listing
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('folders_and_documents_tables');
    }
};
