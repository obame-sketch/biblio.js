<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Identifiants invalides'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user->load('role.permissions'),
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Déconnexion réussie']);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:100',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'telephone' => 'nullable|string|max:20',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['role_id'] = 2; // emprunteur par défaut

        $user = User::create($validated);
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user->load('role'),
        ], 201);
    }
}