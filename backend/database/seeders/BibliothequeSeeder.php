<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class BibliothequeSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            ['nom_role' => 'gerant'],
            ['nom_role' => 'emprunteur'],
        ];
        DB::table('roles')->insert($roles);

        $permissions = [
            ['nom_permission' => 'ajouter_livre'],
            ['nom_permission' => 'modifier_livre'],
            ['nom_permission' => 'supprimer_livre'],
            ['nom_permission' => 'gerer_stock'],
            ['nom_permission' => 'gerer_emprunts'],
            ['nom_permission' => 'gerer_utilisateurs'],
            ['nom_permission' => 'consulter_livres'],
            ['nom_permission' => 'rechercher_livres'],
            ['nom_permission' => 'emprunter_livre'],
            ['nom_permission' => 'retourner_livre'],
            ['nom_permission' => 'voir_historique'],
            ['nom_permission' => 'telecharger_ressource'],
        ];
        DB::table('permissions')->insert($permissions);

        $rolePermissions = [
            [1, 1], [1, 2], [1, 3], [1, 4], [1, 5], [1, 6], [1, 7], [1, 8], [1, 9], [1, 10], [1, 11], [1, 12],
            [2, 7], [2, 8], [2, 9], [2, 10], [2, 11], [2, 12],
        ];
        foreach ($rolePermissions as $rp) {
            DB::table('role_permissions')->insert([
                'role_id' => $rp[0],
                'permission_id' => $rp[1],
            ]);
        }

        DB::table('users')->insert([
            [
                'nom' => 'Gerant Principal',
                'email' => 'gerant@bibliotheque.com',
                'password' => Hash::make('password123'),
                'telephone' => '066000000',
                'role_id' => 1,
            ],
            [
                'nom' => 'Jean Etudiant',
                'email' => 'jean@bibliotheque.com',
                'password' => Hash::make('password123'),
                'telephone' => '077000000',
                'role_id' => 2,
            ],
        ]);

        DB::table('categories')->insert([
            ['nom_categorie' => 'Informatique', 'description' => 'Livres informatiques'],
            ['nom_categorie' => 'Programmation', 'description' => 'Développement logiciel'],
            ['nom_categorie' => 'Base de données', 'description' => 'SQL et PostgreSQL'],
            ['nom_categorie' => 'Réseaux', 'description' => 'Administration réseau'],
        ]);

        DB::table('auteurs')->insert([
            ['nom' => 'Dupont', 'prenom' => 'Jean', 'nationalite' => 'Française'],
            ['nom' => 'Ngoma', 'prenom' => 'Patrick', 'nationalite' => 'Gabonaise'],
        ]);

        $livre1 = DB::table('livres')->insertGetId([
            'titre' => 'Apprendre Laravel',
            'isbn' => '9781234567890',
            'description' => 'Guide complet Laravel',
            'annee_publication' => 2024,
            'auteur_id' => 1,
            'categorie_id' => 2,
        ]);

        $livre2 = DB::table('livres')->insertGetId([
            'titre' => 'PostgreSQL Avancé',
            'isbn' => '9789876543210',
            'description' => 'Administration PostgreSQL',
            'annee_publication' => 2023,
            'auteur_id' => 2,
            'categorie_id' => 3,
        ]);

        DB::table('stock_livres')->insert([
            ['quantite_totale' => 10, 'quantite_disponible' => 10, 'seuil_alerte' => 2, 'livre_id' => $livre1],
            ['quantite_totale' => 5, 'quantite_disponible' => 5, 'seuil_alerte' => 1, 'livre_id' => $livre2],
        ]);
    }
}