<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('telephone')->nullable();
            $table->string('ville')->nullable();
            $table->string('sexe')->nullable();
            $table->date('date_naissance')->nullable();
            $table->string('categorie_permis')->nullable();
            $table->string('auto_ecole')->nullable();
            $table->string('horaire_formation')->nullable();
            $table->boolean('has_cni')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['telephone', 'ville', 'sexe', 'date_naissance', 'categorie_permis', 'auto_ecole', 'horaire_formation', 'has_cni']);
        });
    }
};
