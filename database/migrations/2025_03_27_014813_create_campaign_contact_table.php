<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampaignContactTable extends Migration
{
    public function up(): void
    {
        Schema::create('campaign_contact', function (Blueprint $table) {
            $table->uuid('campaign_id');  // Usa uuid para campaign_id
            $table->uuid('contact_id');   // Usa uuid para contact_id
            $table->timestamps();

            // Claves foráneas
            $table->foreign('campaign_id')->references('id')->on('campaigns')->onDelete('cascade');
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');

            // Asegurarse de que cada par de campaña y contacto sea único
            $table->primary(['campaign_id', 'contact_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_contact');
    }
}
