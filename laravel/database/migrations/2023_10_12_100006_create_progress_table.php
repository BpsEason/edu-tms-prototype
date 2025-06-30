<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database->Schema\Blueprint;
use Illuminate->Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('chapter_id')->nullable()->constrained()->onDelete('cascade'); // Null for overall course progress
            $table->float('progress_percentage')->default(0);
            $table->timestamps();
            $table->unique(['user_id', 'course_id', 'chapter_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('progress');
    }
};
