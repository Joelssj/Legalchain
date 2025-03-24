<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionsTable extends Migration
{
    public function up()
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->uuid('id')->primary();  // Asegúrate de que 'id' sea UUID
            $table->uuid('user_id');  // 'user_id' debe ser UUID
            $table->string('plan');  // Plan (basic, premium, etc.)
            $table->timestamp('start_date');
            $table->timestamp('end_date')->nullable();  // Sin fecha para el plan básico
            $table->timestamps();

            // Definir la clave foránea para la relación con 'users'
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('subscriptions');
    }
}

