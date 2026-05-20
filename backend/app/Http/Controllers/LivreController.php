<?php

namespace App\Http\Controllers;

use App\Models\Livre;
use Illuminate\Http\Request;

class LivreController extends Controller
{
    public function index(Request $request)
    {
        $livres = Livre::with(['auteur', 'categorie', 'stock'])->get();
        return response()->json($livres);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'isbn' => 'nullable|string|unique:livres,isbn',
            'description' => 'nullable|string',
            'annee_publication' => 'nullable|integer',
            'auteur_id' => 'nullable|exists:auteurs,id',
            'categorie_id' => 'nullable|exists:categories,id',
        ]);

        $livre = Livre::create($validated);
        return response()->json($livre, 201);
    }

    public function show($id)
    {
        $livre = Livre::with(['auteur', 'categorie', 'stock'])->findOrFail($id);
        return response()->json($livre);
    }

    public function update(Request $request, $id)
    {
        $livre = Livre::findOrFail($id);
        
        $validated = $request->validate([
            'titre' => 'sometimes|string|max:255',
            'isbn' => 'sometimes|string|unique:livres,isbn,' . $livre->id,
            'description' => 'nullable|string',
            'annee_publication' => 'nullable|integer',
            'auteur_id' => 'nullable|exists:auteurs,id',
            'categorie_id' => 'nullable|exists:categories,id',
        ]);

        $livre->update($validated);
        return response()->json($livre);
    }

    public function destroy($id)
    {
        $livre = Livre::findOrFail($id);
        $livre->delete();
        return response()->json(null, 204);
    }
}