<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use Illuminate\Http\Request;

class CategorieController extends Controller
{
    public function index()
    {
        return response()->json(Categorie::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom_categorie' => 'required|string|max:100|unique:categories',
            'description' => 'nullable|string',
        ]);

        $categorie = Categorie::create($validated);
        return response()->json($categorie, 201);
    }

    public function show($id)
    {
        return response()->json(Categorie::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $categorie = Categorie::findOrFail($id);
        $categorie->update($request->all());
        return response()->json($categorie);
    }

    public function destroy($id)
    {
        Categorie::destroy($id);
        return response()->json(null, 204);
    }
}