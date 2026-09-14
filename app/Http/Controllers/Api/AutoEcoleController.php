<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\JsonResponse;

class AutoEcoleController extends Controller
{
    /**
     * List all auto-écoles available for a Flutter dropdown.
     */
    public function index(): JsonResponse
    {
        // Une école ne doit être proposée que si elle possède un compte auto-école
        // capable de recevoir et traiter les demandes.
        $autoEcoles = Team::query()
            ->whereHas('members', fn ($query) => $query->where('users.role', 'auto_ecole'))
            ->select(['id', 'name', 'slug'])
            ->orderBy('name')
            ->get()
            ->map(fn (Team $team) => [
                'id' => $team->id,
                'name' => $team->name,
                'slug' => $team->slug,
            ]);

        return response()->json([
            'auto_ecoles' => $autoEcoles,
        ]);
    }
}
