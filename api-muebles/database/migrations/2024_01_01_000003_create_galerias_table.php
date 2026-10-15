<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void {
        Schema::create("galerias", function (Blueprint $table) {
            $table->id();
            $table->foreignId("mueble_id")->constrained()->onDelete("cascade");
            $table->string("url_foto"); // /uploads/muebles/sofa-01.jpg
            $table->integer("orden")->default(0);
            $table->boolean("is_principal")->default(false);
            $table->boolean("is_active")->default(true);
            $table->timestamps();
            
            $table->index(["mueble_id", "orden"]);
        });
    }
    public function down(): void {
        Schema::dropIfExists("galerias");
    }
};
