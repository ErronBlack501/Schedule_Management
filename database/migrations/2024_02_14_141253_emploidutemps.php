<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('emploidutemps', function (Blueprint $table) {
            $table->ulid('id');
            $table->string('Cours');
            $table->datetime('DebutCours');
            $table->datetime('FinCours');
            $table->ulid('salle_id');
            $table->ulid('prof_id');
            $table->ulid('classe_id');
            $table->timestamps();
            $table->primary('id');
            $table->foreign('prof_id')->references('id')->on('professeurs')->cascadeOnDelete();
            $table->foreign('classe_id')->references('id')->on('classes')->cascadeOnDelete();
            $table->foreign('salle_id')->references('id')->on('salles')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emploidutemps');
    }
};

