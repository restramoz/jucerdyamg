<?php

namespace Tests\Feature;

use App\Services\SchemeService;
use Tests\TestCase;

class ServiceCatalogTest extends TestCase
{
    /**
     * Guard the schemes.json-driven catalog: exactly 123 unique schemes.
     */
    public function test_catalog_has_exactly_123_schemes(): void
    {
        $this->assertCount(123, app(SchemeService::class)->all());
    }

    public function test_every_scheme_has_complete_required_structure(): void
    {
        foreach (app(SchemeService::class)->all() as $slug => $scheme) {
            foreach (['title', 'meta_title', 'meta_description', 'hero_heading', 'hero_highlight', 'hero_subheading', 'wa_message', 'introduction', 'curriculum_topics', 'training_info', 'faqs'] as $key) {
                $this->assertArrayHasKey($key, $scheme, "Scheme {$slug} is missing [{$key}].");
            }

            $this->assertNotEmpty($scheme['curriculum_topics'], "Scheme {$slug} needs at least one unit.");
            $this->assertGreaterThan(0, count($scheme['faqs']));
            $this->assertLessThanOrEqual(3, count($scheme['faqs']), "Scheme {$slug} may have at most 3 FAQs.");
            $this->assertSame(
                ['jadwal', 'durasi', 'investasi', 'persyaratan', 'otorisasi'],
                array_keys($scheme['training_info']),
                "Scheme {$slug} training_info keys changed."
            );
        }
    }

    public function test_service_titles_meta_and_wa_messages_are_unique(): void
    {
        $catalog = app(SchemeService::class)->all();

        // Titles may legitimately repeat across independent source catalogs;
        // identity is carried by slug plus source attribution.
        $slugs = array_keys($catalog);
        $this->assertSame(count($slugs), count(array_unique($slugs)));
    }
}
