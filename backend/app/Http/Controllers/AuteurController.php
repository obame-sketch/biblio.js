<?php

namespace App\Http\Controllers;

use App\Models\Auteur;
use Illuminate\Http\Request;

class AuteurController extends Controller
{
    public function index()
    {
        return response()->json(Auteur::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:100',
            'prenom' => 'nullable|string|max:100',
            'nationalite' => 'nullable|string|max:100',
        ]);

        $auteur = Auteur::create($validated);
        return response()->json($auteur, 201);
    }

    public function show($id)
    {
        return response()->json(Auteur::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $auteur = Auteur::findOrFail($id);
        $validated = $request->validate([
            'nom' => 'sometimes|string|max:100',
            'prenom' => 'sometimes|string|max:100',
            'nationalite' => 'sometimes|string|max:100',
        ]);
        
        $auteur->update($validated);
        return response()->json($auteur);
    }

    public function destroy($id)
    {
        Auteur::destroy($id);
        return response()->json(null, 204);
    }
}