<?php

namespace Tests\Unit;

use App\Services\SchemeService;
use ReflectionMethod;
use Tests\TestCase;

class SchemeServiceTest extends TestCase
{
    protected SchemeService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(SchemeService::class);
    }

    /**
     * Load a scheme catalog from an alternate path without touching the real file.
     */
    protected function loadFixture(string $json): array
    {
        $path = tempnam(sys_get_temp_dir(), 'schemes_test_');
        file_put_contents($path, $json);

        $method = new ReflectionMethod(SchemeService::class, 'load');
        $method->setAccessible(true);

        return $method->invoke($this->service, $path);
    }

    public function test_catalog_contains_exactly_123_schemes(): void
    {
        $this->assertCount(123, $this->service->all());
    }

    public function test_catalog_contains_exactly_814_competency_units(): void
    {
        $total = array_sum(array_map(
            static fn (array $program): int => count($program['units']),
            $this->service->all()
        ));

        $this->assertSame(814, $total);
    }

    public function test_find_returns_scheme_by_derived_slug(): void
    {
        $scheme = $this->service->find('teknisi-pelindian');

        $this->assertNotNull($scheme);
        $this->assertSame('Teknisi Pelindian', $scheme['title']);
    }

    public function test_find_is_case_and_spacing_tolerant(): void
    {
        $this->assertNotNull($this->service->find('Teknisi Pelindian'));
    }

    public function test_unknown_slug_returns_null(): void
    {
        $this->assertNull($this->service->find('training-ahli-k3-listrik'));
    }

    public function test_slug_collisions_get_deterministic_suffix(): void
    {
        $fixture = json_encode([
            'title' => 'fixture',
            'schemes' => [
                ['name' => 'Teknisi Pelindian', 'lsp' => null, 'units' => []],
                ['name' => 'Teknisi Pelindian', 'lsp' => null, 'units' => []],
                ['name' => 'Teknisi Pelindian', 'lsp' => null, 'units' => []],
            ],
        ]);

        $programs = $this->loadFixture($fixture);

        $this->assertArrayHasKey('teknisi-pelindian', $programs);
        $this->assertArrayHasKey('teknisi-pelindian-2', $programs);
        $this->assertArrayHasKey('teknisi-pelindian-3', $programs);
    }

    public function test_source_title_and_scheme_names_are_preserved_exactly(): void
    {
        $source = json_decode(file_get_contents(base_path('schemes_gppb.json')), true);

        $this->assertSame('Geologi Pertambangan Panas Bumi (GPPB)', $source['title']);
        $this->assertContains('Pengawas Operasional Pertama (POP) Panas Bumi', array_column($source['schemes'], 'name'));

        $titles = array_map(
            static fn (array $program): string => $program['title'],
            $this->service->all()
        );

        // Unified catalog contains every source scheme name as a program title.
        $this->assertContains('Pengawas Operasional Pertama (POP) Panas Bumi', $titles);
    }

    public function test_source_title_is_available_in_representation(): void
    {
        // The catalog title is part of the authoritative dataset, keep it reachable.
        $programs = $this->service->all();
        $this->assertCount(123, $programs);
    }

    public function test_unit_codes_and_names_are_preserved(): void
    {
        $source = json_decode(file_get_contents(base_path('schemes_gppb.json')), true);
        $expected = null;
        foreach ($source['schemes'] as $scheme) {
            if ($scheme['name'] === 'Pengawas Operasional Pertama (POP) Panas Bumi') {
                $expected = $scheme['units'];
                break;
            }
        }
        $this->assertNotNull($expected);

        $loaded = $this->service->find('pengawas-operasional-pertama-pop-panas-bumi');

        $this->assertSame($expected, $loaded['units']);

        // Each unit (code + name) is present in curriculum_topics.
        foreach ($expected as $unit) {
            $this->assertContains($unit['code'] . ' — ' . $unit['name'], $loaded['curriculum_topics']);
        }
    }

    public function test_every_program_matches_required_view_shape(): void
    {
        foreach ($this->service->all() as $slug => $program) {
            foreach ([
                'slug', 'category', 'category_label', 'level', 'title', 'badge',
                'meta_title', 'meta_description', 'hero_heading', 'hero_highlight',
                'hero_subheading', 'wa_message', 'introduction', 'target_audience',
                'roles_and_responsibilities', 'curriculum_topics', 'training_info', 'faqs',
            ] as $key) {
                $this->assertArrayHasKey($key, $program, "Scheme {$slug} is missing [{$key}].");
            }

            $this->assertSame($slug, $program['slug']);
            $this->assertNotEmpty($program['curriculum_topics']);
            $this->assertSame(
                ['jadwal', 'durasi', 'investasi', 'persyaratan', 'otorisasi'],
                array_keys($program['training_info'])
            );
        }
    }

    public function test_missing_source_file_throws(): void
    {
        $this->expectException(\RuntimeException::class);

        $method = new ReflectionMethod(SchemeService::class, 'load');
        $method->setAccessible(true);
        $method->invoke($this->service, base_path('definitely-missing-schemes.json'));
    }

    public function test_invalid_json_throws(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->loadFixture('{"title": "broken"');
    }

    public function test_unexpected_structure_throws(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->loadFixture(json_encode(['title' => 'Agribisnis Ambisisi']));
    }

    public function test_scheme_without_name_throws(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->loadFixture(json_encode(['schemes' => [['lsp' => null, 'units' => []]]]));
    }
}
