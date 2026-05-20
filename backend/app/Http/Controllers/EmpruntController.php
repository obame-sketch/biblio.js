<?php

namespace App\Http\Controllers;

use App\Models\Emprunt;
use App\Models\Livre;
use App\Models\StockLivre;
use Illuminate\Http\Request;

class EmpruntController extends Controller
{
    public function index()
    {
        return response()->json(Emprunt::with(['user', 'livre'])->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'livre_id' => 'required|exists:livres,id',
            'date_retour_prevue' => 'required|date',
        ]);

        // Vérifier la disponibilité du livre dans une transaction pour éviter les conditions de course
        return DB::transaction(function () use ($validated) {
            $stock = StockLivre::where('livre_id', $validated['livre_id'])->lockForUpdate()->firstOrFail();
            
            if ($stock->quantite_disponible < 1) {
                throw new \Exception('Livre non disponible');
            }

            // Ajouter la date d'emprunt et le statut initial
            $empruntData = array_merge($validated, [
                'date_emprunt' => now(),
                'statut' => 'en_cours'
            ]);

            $emprunt = Emprunt::create($empruntData);
            
            $stock->decrement('quantite_disponible');
            
            return response()->json($emprunt, 201);
        });
    }

    public function retourner($id)
    {
        $emprunt = Emprunt::findOrFail($id);
        
        if ($emprunt->statut === 'termine') {
            return response()->json(['message' => 'Emprunt déjà terminé'], 400);
        }

        $emprunt->update([
            'date_retour_effective' => now(),
            'statut' => 'termine'
        ]);

        $stock = StockLivre::where('livre_id', $emprunt->livre_id)->firstOrFail();
        $stock->increment('quantite_disponible');

        return response()->json($emprunt);
    }
}