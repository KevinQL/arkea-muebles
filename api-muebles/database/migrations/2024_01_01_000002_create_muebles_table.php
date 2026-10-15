<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void {
        Schema::create("muebles", function (Blueprint $table) {
            $table->id();
            $table->foreignId("categoria_id")->nullable()->constrained("categorias")->nullOnDelete();
            $table->string("nombre");
            $table->string("slug")->unique();
            $table->decimal("precio", 10, 2);
            $table->enum("estado", ["nuevo", "usado", "renovado", "agendado"])->default("nuevo");
            $table->string("ubicacion")->default("Andahuaylas");
            $table->text("descripcion_corta")->nullable();
            $table->longText("descripcion_completa")->nullable();
            $table->string("galeria_principal")->nullable();
            $table->json("galeria")->nullable();
            $table->json("materiales")->nullable();
            $table->json("medidas")->nullable();
            $table->json("especificaciones")->nullable();
            $table->json("informacion_adicional")->nullable();
            $table->boolean("is_active")->default(true);
            $table->timestamps();
            $table->index(["estado", "is_active"]);
        });
    }
    public function down(): void {
        Schema::dropIfExists("muebles");
    }
};
