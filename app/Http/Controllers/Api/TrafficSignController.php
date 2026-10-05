<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TrafficSignAiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TrafficSignController extends Controller
{
    public function __construct(
        protected TrafficSignAiService $aiService
    ) {}

    /**
     * Analyse une photo de panneau de signalisation transmise par l'application mobile.
     */
    public function analyze(Request $request): JsonResponse
    {
        $request->validate([
            'image' => ['required', 'file', 'image', 'max:10240'], // 10MB max
        ], [
            'image.required' => 'Une image est requise pour effectuer l’analyse.',
            'image.image' => 'Le fichier envoyé doit être une image valide (jpg, png, webp...).',
            'image.max' => 'La taille de l’image ne doit pas dépasser 10 Mo.',
        ]);

        $result = $this->aiService->analyze($request->file('image'));

        return response()->json([
            'status' => 'success',
            'data' => $result,
        ]);
    }
}
