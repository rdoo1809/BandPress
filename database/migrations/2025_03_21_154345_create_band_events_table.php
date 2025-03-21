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
        Schema::create('band_events', function (Blueprint $table) {
            $table->id();

            $table->foreignId('band_site_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('day');
            $table->string('month');
            $table->text('description');
            $table->string('venue_link');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('band_events');
    }
};
