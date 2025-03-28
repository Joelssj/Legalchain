<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta las migraciones.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Verificar si la columna 'verification_code' ya existe antes de agregarla
            if (!Schema::hasColumn('users', 'verification_code')) {
                $table->string('verification_code')->nullable();  // Código de verificación
            }

            // Verificar si la columna 'verification_code_status' ya existe antes de agregarla
            if (!Schema::hasColumn('users', 'verification_code_status')) {
                $table->string('verification_code_status')->default('active'); // Estado del código
            }

            // Verificar si la columna 'email_verified_at' ya existe antes de agregarla
            if (!Schema::hasColumn('users', 'email_verified_at')) {
                $table->timestamp('email_verified_at')->nullable(); // Fecha de verificación del correo
            }
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Eliminar las columnas de verificación
            $table->dropColumn('verification_code');
            $table->dropColumn('verification_code_status');
            $table->dropColumn('email_verified_at');
        });
    }
};





