<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('band_sites', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('repo_url');
            $table->string('deployment_url');

            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('band_sites');
    }
};
