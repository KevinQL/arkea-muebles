<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void {
        Schema::create("whatsapp_configs", function (Blueprint $table) {
            $table->id();
            $table->enum("tipo", ["general", "vendedor"])->default("general");
            $table->string("numero_telefono", 20); // +51987654321
            $table->string("nombre_usuario")->nullable(); // "Vendedor Principal" o nombre del vendedor
            $table->text("mensaje_default")->nullable(); 
            $table->string("api_key")->nullable(); // Para WhatsApp Business API si aplica
            
            $table->boolean("is_active")->default(true);
            $table->integer("orden")->default(0); // Para ordenar en lista
            $table->timestamps();
            
            $table->index(["tipo", "numero_telefono"]);
        });
    }
    public function down(): void {
        Schema::dropIfExists("whatsapp_configs");
    }
};
