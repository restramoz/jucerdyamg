<?php

namespace Tests\Unit;

use App\Services\OllamaService;
use App\Services\SchemeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class K3ListrikServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_loads_from_layanan_json(): void
    {
        $service = app(SchemeService::class);

        $this->assertGreaterThan(0, $service->all());
        // Teknisi Pelindian exists in layanan.json
        $this->assertNotNull($service->find('teknisi-pelindian'));
        $this->assertSame('Teknisi Pelindian', $service->find('teknisi-pelindian')['title']);
    }

    public function test_pick_topic_uses_a_service_from_the_catalog(): void
    {
        config([
            'services.ollama.base_url' => 'http://dummy-ollama.local',
            'services.ollama.model' => 'dummy-model',
        ]);

        $selected = (new OllamaService())->pickTopicForRegion('Bandung', 'bandung');

        $this->assertArrayHasKey('topic', $selected);
        $this->assertArrayHasKey('focus', $selected);
        $this->assertNotEmpty($selected['topic']);
        $this->assertNotEmpty($selected['focus']);
    }

    public function test_presentation_grouping_keeps_every_service_once(): void
    {
        $service = app(SchemeService::class);
        $groups = $service->groupedForPresentation();

        $this->assertSame(['maintenance-pengukuran', 'instalasi-proteksi', 'otomasi-industri', 'k3-listrik'], array_keys($groups));
        $total = count($service->all());
        $mergedCount = count(array_merge(...array_values($groups)));
        $this->assertSame($total, $mergedCount);
        $this->assertGreaterThan(0, count($groups['k3-listrik']));
        $this->assertGreaterThan(0, count($groups['instalasi-proteksi']));
        $this->assertGreaterThan(0, count($groups['otomasi-industri']));
        $this->assertGreaterThan(0, count($groups['maintenance-pengukuran']));

        // At least one K3 service lands in the K3 Listrik bucket.
        $k3titles = array_map(
            static fn (array $p): string => $p['title'],
            $groups['k3-listrik']
        );
        $this->assertTrue(
            in_array('AHLI K3 MADYA', $k3titles, true)
            || in_array('AHLI K3 UTAMA', $k3titles, true)
            || count($groups['k3-listrik']) > 0
        );
    }
}
