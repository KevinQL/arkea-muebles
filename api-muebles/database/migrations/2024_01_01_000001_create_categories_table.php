<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void {
        Schema::create("categorias", function (Blueprint $table) {
            $table->id();
            $table->string("nombre"); 
            $table->enum("tipo", ["plana", "jerarquica"])->default("plana");
            $table->string("subcategoria")->nullable(); 
            $table->text("descripcion")->nullable();
            $table->string("icono")->nullable(); 
            $table->string("thumbnail")->nullable();
            $table->boolean("is_active")->default(true);
            $table->timestamps();
            $table->index(["nombre", "tipo"]);
        });
    }
    public function down(): void {
        Schema::dropIfExists("categorias");
    }
};
