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
        Schema::create('contas', function (Blueprint $table) {
            $table->id();
            $table->integer('codigo');
            $table->integer('codigo_venda')->nullable();
            $table->integer('codigo_cliente');
            $table->decimal('valor');
            $table->date('data_vencimento');
            $table->date('data_envio');
            $table->integer('cilcus')->default(1);
            $table->integer('tipo');
            $table->integer('status')->default(1);
            $table->string('loja');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contas');
    }
};
