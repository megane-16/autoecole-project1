<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Mettre à jour les informations du profil utilisateur.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'telephone' => [
                'nullable',
                'string',
                'max:30',
                Rule::unique('users', 'telephone')->ignore($user->id),
            ],
            'ville' => ['nullable', 'string', 'max:255'],
            'photo' => ['nullable', 'file', 'image', 'max:5120'], // 5MB max
        ], [
            'telephone.unique' => 'Ce numéro de téléphone est déjà utilisé par un autre compte.',
            'photo.image' => 'Le fichier doit être une image valide.',
            'photo.max' => 'La photo de profil ne doit pas dépasser 5 Mo.',
        ]);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('profile-photos', 'public');
            $validated['photo_profil'] = Storage::url($path);
        }

        $user->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Profil mis à jour avec succès.',
            'user' => $user->fresh(),
        ]);
    }

    /**
     * Modifier le mot de passe de manière sécurisée.
     */
    public function changePassword(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', Password::min(8), 'confirmed'],
        ], [
            'current_password.required' => 'Le mot de passe actuel est obligatoire.',
            'password.min' => 'Le nouveau mot de passe doit comporter au moins 8 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
        ]);

        if (! Hash::check($validated['current_password'], $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Le mot de passe actuel est incorrect.',
                'errors' => [
                    'current_password' => ['Le mot de passe actuel est incorrect.'],
                ],
            ], 422);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Mot de passe modifié avec succès.',
        ]);
    }
}
