<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TrafficSignTest extends TestCase
{
    use RefreshDatabase;

    public function test_analyze_requires_an_image(): void
    {
        $response = $this->postJson('/api/traffic-signs/analyze', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['image']);
    }

    public function test_analyze_identifies_traffic_sign_successfully(): void
    {
        Http::fake([
            'openrouter.ai/*' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => json_encode([
                                'is_traffic_sign' => true,
                                'sign_name' => 'Arrêt obligatoire (Stop)',
                                'category' => 'Interdiction',
                                'meaning' => 'Arrêt absolu imposé à l’intersection.',
                                'action_required' => 'Marquer l’arrêt complet à la ligne.',
                                'penalty_risk' => 'Amende de 4ème classe.',
                                'message' => 'Panneau identifié avec succès.',
                            ]),
                        ],
                    ],
                ],
            ], 200),
        ]);

        $file = UploadedFile::fake()->create('stop_sign.jpg', 100, 'image/jpeg');

        $response = $this->postJson('/api/traffic-signs/analyze', [
            'image' => $file,
        ]);

        $response->assertOk()
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'is_traffic_sign' => true,
                    'sign_name' => 'Arrêt obligatoire (Stop)',
                    'category' => 'Interdiction',
                ],
            ]);
    }

    public function test_analyze_identifies_non_traffic_sign_image(): void
    {
        Http::fake([
            'openrouter.ai/*' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => json_encode([
                                'is_traffic_sign' => false,
                                'message' => 'Ceci n’est pas un panneau de signalisation.',
                            ]),
                        ],
                    ],
                ],
            ], 200),
        ]);

        $file = UploadedFile::fake()->create('cat.jpg', 100, 'image/jpeg');

        $response = $this->postJson('/api/traffic-signs/analyze', [
            'image' => $file,
        ]);

        $response->assertOk()
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'is_traffic_sign' => false,
                    'message' => 'Ceci n’est pas un panneau de signalisation.',
                ],
            ]);
    }
}
