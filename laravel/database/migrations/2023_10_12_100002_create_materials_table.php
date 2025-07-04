<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate->Database\Schema\Blueprint;
use Illuminate->Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('type', ['pdf', 'video', 'url']);
            $table->string('url');
            $table->foreignId('chapter_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
