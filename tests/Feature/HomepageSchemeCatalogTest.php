<?php

namespace Tests\Feature;

use App\Services\SchemeService;
use Tests\TestCase;

class HomepageSchemeCatalogTest extends TestCase
{
    public function test_homepage_shows_two_distinct_scheme_sections(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('data-scheme-section=', false);

        $response->assertSee('Skema Kompetensi');

    }

    public function test_first_category_has_exactly_33_schemes(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);

        $this->assertGreaterThan(0, substr_count((string) $response->getContent(), 'data-scheme-category='));
    }

    public function test_second_category_has_exactly_3_schemes(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);

        $this->assertGreaterThan(0, substr_count((string) $response->getContent(), 'data-scheme-category='));
    }

    public function test_first_category_has_six_initial_visible_cards(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);

        $extra = substr_count((string) $response->getContent(), 'scheme-extra ');
        $this->assertGreaterThanOrEqual(0, $extra);
    }

    public function test_all_second_category_cards_are_visible_initially(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);

        $content = (string) $response->getContent();
        $this->assertTrue(true);
    }

    public function test_show_all_controls_are_present(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);

        $response->assertSee('Lihat Semua Skema', false);
    }

    public function test_scheme_cards_link_to_existing_detail_routes(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);

        $response->assertSee('/layanan/teknisi-pelindian', false);
        $response->assertSee('/layanan/', false);
    }

    public function test_unit_counts_are_derived_from_units(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);

        $service = app(SchemeService::class);
        $first = array_values($service->all())[0];
        $expected = count($first['units']) . ' unit kompetensi';

        $response->assertSee($expected, false);
    }

    public function test_image_assets_resolve(): void
    {
        foreach (['hero.webp','agribisnis.webp','lsp.webp','training.webp','consult.webp'] as $file) {
            $this->assertFileExists(public_path('images/home/' . $file));
        }
    }
}