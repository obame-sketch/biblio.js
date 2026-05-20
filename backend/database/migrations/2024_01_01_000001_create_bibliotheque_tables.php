<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('nom_role');
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('nom_permission');
            $table->timestamps();
        });

        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->foreignId('permission_id')->constrained()->onDelete('cascade');
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('telephone')->nullable();
            $table->foreignId('role_id')->constrained();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('nom_categorie');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('auteurs', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('prenom');
            $table->string('nationalite')->nullable();
            $table->timestamps();
        });

        Schema::create('livres', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->string('isbn')->unique();
            $table->text('description')->nullable();
            $table->integer('annee_publication')->nullable();
            $table->foreignId('auteur_id')->nullable()->constrained();
            $table->foreignId('categorie_id')->nullable()->constrained();
            $table->timestamps();
        });

        Schema::create('stock_livres', function (Blueprint $table) {
            $table->id();
            $table->integer('quantite_totale');
            $table->integer('quantite_disponible');
            $table->integer('seuil_alerte')->default(0);
            $table->foreignId('livre_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('emprunts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('livre_id')->constrained();
            $table->date('date_emprunt');
            $table->date('date_retour_prevue');
            $table->date('date_retour_effective')->nullable();
            $table->enum('statut', ['en_cours', 'termine'])->default('en_cours');
            $table->timestamps();
        });

        Schema::create('ressources_numeriques', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->string('fichier');
            $table->string('type_fichier');
            $table->foreignId('user_id')->constrained();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ressources_numeriques');
        Schema::dropIfExists('emprunts');
        Schema::dropIfExists('stock_livres');
        Schema::dropIfExists('livres');
        Schema::dropIfExists('auteurs');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('users');
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};