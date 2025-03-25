<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('band_releases', function (Blueprint $table) {
            $table->id();

            $table->foreignId('band_site_id')->constrained()->onDelete('cascade');
            $table->string('host_link');
            $table->string('cover_image');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('band_releases');
    }
};
