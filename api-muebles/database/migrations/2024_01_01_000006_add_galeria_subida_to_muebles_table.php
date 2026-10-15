<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void {
        Schema::table("muebles", function (Blueprint $table) {
            $table->string("galeria_subida")->nullable()->after("galeria_principal");
        });
    }
    public function down(): void {
        Schema::table("muebles", function (Blueprint $table) {
            $table->dropColumn("galeria_subida");
        });
    }
};
