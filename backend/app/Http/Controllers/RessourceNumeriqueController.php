<?php

namespace App\Http\Controllers;

use App\Models\RessourceNumerique;
use Illuminate\Http\Request;

class RessourceNumeriqueController extends Controller
{
    public function index()
    {
        $ressources = RessourceNumerique::with('user')->get();
        return response()->json($ressources);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'fichier' => 'required|string|max:255',
            'type_fichier' => 'required|string|max:50',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $ressource = RessourceNumerique::create($validated);
        return response()->json($ressource, 201);
    }

    public function show($id)
    {
        $ressource = RessourceNumerique::with('user')->findOrFail($id);
        return response()->json($ressource);
    }

    public function update(Request $request, $id)
    {
        $ressource = RessourceNumerique::findOrFail($id);
        $validated = $request->validate([
            'titre' => 'sometimes|string|max:255',
            'fichier' => 'sometimes|string|max:255',
            'type_fichier' => 'sometimes|string|max:50',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $ressource->update($validated);
        return response()->json($ressource);
    }

    public function destroy($id)
    {
        $ressource = RessourceNumerique::findOrFail($id);
        $ressource->delete();
        return response()->json(null, 204);
    }
}