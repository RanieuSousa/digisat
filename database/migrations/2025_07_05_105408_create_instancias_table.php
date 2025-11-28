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
        Schema::create('instancias', function (Blueprint $table) {
            $table->id();
            $table->string('instanceName');
            $table->string('instanceId');
            $table->string('integration');
            $table->string('webhookWaBusiness')->nullable();
            $table->string('accessTokenWaBusiness')->nullable();
            $table->string('status');
            $table->string('hash');
            $table->string('url');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instancias');
    }
};
