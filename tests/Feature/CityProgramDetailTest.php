<?php

namespace Tests\Feature;

use Tests\TestCase;

class CityProgramDetailTest extends TestCase
{
    /**
     * Ensure the main agricultural programs render with city context.
     */
    public function test_main_programs_render_canonical_content_with_city_context(): void
    {
        // 1. Teknisi Pelindian in Bandung
        $response = $this->get(route('city.program.detail', ['city' => 'bandung', 'program' => 'teknisi-pelindian']));
        $response->assertStatus(200);
        $response->assertSee('Teknisi Pelindian');
        $content = $response->getContent();
        // Should not show placeholder text
        $this->assertFalse(strpos($content, 'Konten detail program belum tersedia.') !== false);

        // 2. Teknisi Pelindian in Malang
        $response2 = $this->get(route('city.program.detail', ['city' => 'malang', 'program' => 'teknisi-pelindian']));
        $response2->assertStatus(200);
        $response2->assertSee('Teknisi Pelindian');

        // 3. TEKNISI PELINDIAN in Surabaya
        $response3 = $this->get(route('city.program.detail', ['city' => 'surabaya', 'program' => 'teknisi-pelindian']));
        $response3->assertStatus(200);
        $response3->assertSee('Teknisi Pelindian');
    }

    /**
     * Ensure full program slugs resolve to a valid program.
     */
    public function test_full_program_slugs_resolve_correctly(): void
    {
        $response = $this->get(route('city.program.detail', ['city' => 'bandung', 'program' => 'teknisi-pelindian']));
        $response->assertStatus(200);
        $response->assertSee('Teknisi Pelindian');
    }

    /**
     * Invalid city slug should return 404.
     */
    public function test_invalid_city_returns_404(): void
    {
        $response = $this->get(route('city.program.detail', ['city' => 'nonexistent-city', 'program' => 'teknisi-pelindian']));
        $response->assertStatus(404);
    }

    /**
     * Invalid program slug should return 404.
     */
    public function test_invalid_program_returns_404(): void
    {
        $response = $this->get(route('city.program.detail', ['city' => 'bandung', 'program' => 'invalid']));
        $response->assertStatus(404);
    }
}
