<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registration_requests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('password');
            $table->string('telephone')->nullable();
            $table->string('ville')->nullable();
            $table->string('sexe')->nullable();
            $table->date('date_naissance')->nullable();
            $table->string('categorie_permis')->nullable();
            $table->foreignId('team_id')->constrained('teams')->onDelete('cascade');
            $table->string('horaire_formation')->nullable();
            $table->boolean('has_cni')->default(false);
            $table->string('status')->default('pending'); // pending, validated, rejected
            $table->string('validation_code')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registration_requests');
    }
};
