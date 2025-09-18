<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('photos', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('path')->unique();
            $table->unsignedBigInteger('size');
            $table->string('type', 32);
            $table->string('extension', 12);
            $table->unsignedInteger('width')->default(0);
            $table->unsignedInteger('height')->default(0);
            $table->timestamps();

            // indexes for better performance
            $table->index(['name', 'path']);
            $table->index(['size', 'type']);
            $table->index(['extension', 'width', 'height']);
            $table->index('path');
        });

        // جدول وسيط لعلاقة polymorphic many-to-many
        Schema::create('photoables', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('photo_id');
            $table->unsignedBigInteger('photoable_id');
            $table->string('photoable_type', 64);
            $table->timestamps();

            $table->unique(['photo_id', 'photoable_id', 'photoable_type'], 'photoables_unique');
            $table->foreign('photo_id')->references('id')->on('photos')->onDelete('cascade');
            $table->index(['photoable_id', 'photoable_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('photoables');
        Schema::dropIfExists('photos');
    }
};
