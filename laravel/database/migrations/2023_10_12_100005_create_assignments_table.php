<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate->Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('group_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->date('due_date');
            $table->timestamps();
            $table->unique(['user_id', 'course_id']);
            $table->unique(['group_id', 'course_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
