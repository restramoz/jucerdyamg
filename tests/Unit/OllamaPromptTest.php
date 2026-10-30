<?php

namespace Tests\Unit;

use App\Services\OllamaService;
use App\Services\SchemeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OllamaPromptTest extends TestCase
{
    use RefreshDatabase;

    private function fakeSuccessfulOllama(): void
    {
        config([
            'services.ollama.base_url' => 'http://dummy-ollama.local',
            'services.ollama.model' => 'dummy-model',
        ]);

        $content = str_repeat('kata ', 7500);
        Http::fake(['*' => Http::response([
            'message' => ['content' => json_encode([
                'title' => 'Judul Test',
                'slug' => 'judul-test',
                'excerpt' => 'Excerpt test',
                'content' => $content,
                'seo_title' => 'SEO Title Test | Jucerdyamg',
                'meta_description' => 'Meta Description Test',
                'suggested_faqs' => [],
                'image_brief' => [],
            ])],
        ], 200)]);
    }

    public function test_prompts_carry_brand_and_global_context(): void
    {
        $this->fakeSuccessfulOllama();

        (new OllamaService())->generateArticle([
            'city_name' => 'Bandung',
            'city_slug' => 'bandung',
            'word_count' => 7500,
        ]);

        Http::assertSent(function (object $request): bool {
            $system = $request['messages'][0]['content'];
            $user = $request['messages'][1]['content'];

            return str_contains($system, 'Jucerdyamg')
                && str_contains($user, 'layanan dan skema kompetensi');
        });

        // Legacy brand must not be present.
        Http::assertSent(function (object $request): bool {
            $prompts = $request['messages'][0]['content'] . ' ' . $request['messages'][1]['content'];
            return !str_contains($prompts, 'Zonyberg');
        });
    }

    public function test_prompts_exclude_legacy_identifiers(): void
    {
        $this->fakeSuccessfulOllama();

        (new OllamaService())->generateArticle([
            'city_name' => 'Bandung',
            'city_slug' => 'bandung',
            'word_count' => 7500,
        ]);

        Http::assertSent(function (object $request): bool {
            $prompts = $request['messages'][0]['content'] . ' ' . $request['messages'][1]['content'];

            return !str_contains($prompts, 'K3 Listrik')
                && !str_contains($prompts, 'k3-listrik')
                && !str_contains($prompts, 'Keselamatan Kelistrikan');
        });
    }

    public function test_service_slug_injects_targeted_service_context(): void
    {
        $this->fakeSuccessfulOllama();

        $scheme = (new SchemeService())->find('teknisi-pelindian');
        $this->assertNotNull($scheme);
        $title = (string) $scheme['title'];

        (new OllamaService())->generateArticle([
            'city_name' => 'Bandung',
            'city_slug' => 'bandung',
            'word_count' => 7500,
            'service_slug' => 'teknisi-pelindian',
        ]);

        Http::assertSent(function (object $request) use ($title): bool {
            $system = $request['messages'][0]['content'];
            $user = $request['messages'][1]['content'];

            return str_contains($system, 'Program spesifik: ' . $title)
                && str_contains($user, '- Program Spesifik: ' . $title);
        });
    }
}
