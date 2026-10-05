<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TrafficSignAiService
{
    protected ?string $apiKey;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = config('services.openrouter.key', env('OPENROUTER_API_KEY'));
        $this->model = config('services.openrouter.model', env('OPENROUTER_MODEL', 'google/gemini-2.5-flash'));
    }

    /**
     * Analyse une image envoyée par l'apprenant pour identifier s'il s'agit d'un panneau de signalisation.
     */
    public function analyze(UploadedFile|string $image): array
    {
        if (empty($this->apiKey)) {
            Log::warning('TrafficSignAiService: OPENROUTER_API_KEY non configurée.');
            return [
                'is_traffic_sign' => false,
                'message' => 'Service d’analyse IA temporairement indisponible (clé API manquante).',
            ];
        }

        try {
            $imageData = $image instanceof UploadedFile
                ? file_get_contents($image->getRealPath())
                : file_get_contents($image);

            $mimeType = $image instanceof UploadedFile
                ? ($image->getMimeType() ?: 'image/jpeg')
                : 'image/jpeg';

            $base64Image = 'data:' . $mimeType . ';base64,' . base64_encode($imageData);

            $systemPrompt = <<<PROMPT
Tu es un expert instructeur du code de la route et de la sécurité routière (notamment en zone CEMAC/Cameroun et normes internationales de signalisation routière).
Ton rôle est d'analyser l'image fournie par un apprenant conducteur.

INSTRUCTIONS TRÈS STRICTES :
1. Détermine si l'image représente un PANNEAU DE SIGNALISATION ROUTIÈRE (panneau de danger, interdiction, obligation, indication, priorité, fin d'interdiction, etc.).
2. Si l'image n'est PAS un panneau de signalisation (ex: animal, visage, paysage, meuble, nourriture, véhicule seul sans panneau, objet quelconque, texte quelconque), tu DOIS renvoyer `is_traffic_sign: false`.
3. Tu DOIS impérativement répondre UNIQUEMENT en format JSON valide, sans balises markdown ```json et sans texte avant ou après.

Structure JSON attendue si CE N'EST PAS un panneau :
{
  "is_traffic_sign": false,
  "message": "Ceci n'est pas un panneau de signalisation."
}

Structure JSON attendue si C'EST un panneau de signalisation :
{
  "is_traffic_sign": true,
  "sign_name": "Nom officiel du panneau en français (ex: Arrêt obligatoire (Stop), Céder le passage, Limitation de vitesse 50, etc.)",
  "category": "Danger" | "Interdiction" | "Obligation" | "Indication",
  "meaning": "Explication claire et précise de ce que signifie ce panneau pour le conducteur.",
  "action_required": "Comportement immédiat attendu du conducteur (ex: ralentir, s'arrêter, serrer à droite).",
  "penalty_risk": "Risque encouru en cas de non-respect (amende, retrait de points ou danger d'accident).",
  "message": "Panneau identifié avec succès."
}
PROMPT;

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'HTTP-Referer' => config('app.url', 'http://localhost:8000'),
                'X-Title' => 'DriveFlow Road Sign AI',
                'Content-Type' => 'application/json',
            ])->timeout(30)->post('https://openrouter.ai/api/v1/chat/completions', [
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $systemPrompt,
                    ],
                    [
                        'role' => 'user',
                        'content' => [
                            [
                                'type' => 'text',
                                'text' => 'Analyse cette image et réponds strictement au format JSON demandé.',
                            ],
                            [
                                'type' => 'image_url',
                                'image_url' => [
                                    'url' => $base64Image,
                                ],
                            ],
                        ],
                    ],
                ],
                'temperature' => 0.1,
            ]);

            if ($response->failed()) {
                Log::error('OpenRouter API Error: ' . $response->body());
                return [
                    'is_traffic_sign' => false,
                    'message' => 'Erreur lors de la communication avec l’IA d’analyse.',
                ];
            }

            $responseData = $response->json();
            $content = $responseData['choices'][0]['message']['content'] ?? '';

            // Nettoyer d'éventuels backticks markdown JSON
            $cleanJson = trim($content);
            if (str_starts_with($cleanJson, '```json')) {
                $cleanJson = substr($cleanJson, 7);
            } elseif (str_starts_with($cleanJson, '```')) {
                $cleanJson = substr($cleanJson, 3);
            }
            if (str_ends_with($cleanJson, '```')) {
                $cleanJson = substr($cleanJson, 0, -3);
            }
            $cleanJson = trim($cleanJson);

            $parsed = json_decode($cleanJson, true);

            if (!is_array($parsed) || !isset($parsed['is_traffic_sign'])) {
                Log::warning('Format JSON invalide reçu de l’IA: ' . $content);
                return [
                    'is_traffic_sign' => false,
                    'message' => 'Ceci n’est pas un panneau de signalisation.',
                ];
            }

            if ($parsed['is_traffic_sign'] !== true) {
                return [
                    'is_traffic_sign' => false,
                    'message' => $parsed['message'] ?? 'Ceci n’est pas un panneau de signalisation.',
                    'sign_name' => null,
                ];
            }

            return [
                'is_traffic_sign' => true,
                'sign_name' => $parsed['sign_name'] ?? 'Panneau identifié',
                'category' => $parsed['category'] ?? 'Indication',
                'meaning' => $parsed['meaning'] ?? 'Signalisation routière',
                'action_required' => $parsed['action_required'] ?? 'Adapter sa conduite',
                'penalty_risk' => $parsed['penalty_risk'] ?? null,
                'message' => $parsed['message'] ?? 'Panneau analysé avec succès.',
            ];
        } catch (\Throwable $e) {
            Log::error('TrafficSignAiService exception: ' . $e->getMessage());
            return [
                'is_traffic_sign' => false,
                'message' => 'Une erreur est survenue lors de l’analyse.',
            ];
        }
    }
}
