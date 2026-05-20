<?php

namespace App\Http\Controllers;

use App\Models\StockLivre;
use App\Models\Livre;
use Illuminate\Http\Request;

class StockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $stocks = StockLivre::with('livre.auteur', 'livre.categorie')->get();
        return response()->json($stocks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'livre_id' => 'required|exists:livres,id',
            'quantite_totale' => 'required|integer|min:0',
            'quantite_disponible' => 'required|integer|min:0',
            'seuil_alerte' => 'required|integer|min:0',
        ]);

        // Vérifier que le livre n'a pas déjà un stock
        if (StockLivre::where('livre_id', $validated['livre_id'])->exists()) {
            return response()->json(['message' => 'Un stock existe déjà pour ce livre'], 400);
        }

        // S'assurer que la quantité disponible ne dépasse pas la quantité totale
        if ($validated['quantite_disponible'] > $validated['quantite_totale']) {
            return response()->json(['message' => 'La quantité disponible ne peut pas dépasser la quantité totale'], 400);
        }

        $stock = StockLivre::create($validated);
        return response()->json($stock, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $stock = StockLivre::with('livre.auteur', 'livre.categorie')->findOrFail($id);
        return response()->json($stock);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $stock = StockLivre::findOrFail($id);
        
        $validated = $request->validate([
            'quantite_totale' => 'sometimes|integer|min:0',
            'quantite_disponible' => 'sometimes|integer|min:0',
            'seuil_alerte' => 'sometimes|integer|min:0',
        ]);

        // Vérifier la cohérence entre quantite_totale et quantite_disponible
        $quantiteTotale = $validated['quantite_totale'] ?? $stock->quantite_totale;
        $quantiteDisponible = $validated['quantite_disponible'] ?? $stock->quantite_disponible;

        if ($quantiteDisponible > $quantiteTotale) {
            return response()->json(['message' => 'La quantité disponible ne peut pas dépasser la quantité totale'], 400);
        }

        $stock->update($validated);
        return response()->json($stock);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $stock = StockLivre::findOrFail($id);
        $stock->delete();
        return response()->json(null, 204);
    }

    /**
     * Obtenir le stock d'un livre spécifique
     */
    public function getByLivreId($livreId)
    {
        $stock = StockLivre::where('livre_id', $livreId)
            ->with('livre.auteur', 'livre.categorie')
            ->firstOrFail();
        
        return response()->json($stock);
    }
}