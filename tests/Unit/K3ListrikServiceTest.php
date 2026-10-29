<?php

namespace Tests\Unit;

use App\Services\OllamaService;
use App\Services\SchemeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class K3ListrikServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_k3_listrik_catalog_loads_from_json(): void
    {
        $service = app(SchemeService::class);

        $this->assertCount(60, $service->all());
        $this->assertNotNull($service->find('pelatihan-teknisi-k3-listrik'));
        $this->assertSame('Pelatihan Teknisi K3 Listrik', $service->find('pelatihan-teknisi-k3-listrik')['title']);
    }

    public function test_pick_topic_uses_a_k3_listrik_service(): void
    {
        config([
            'services.ollama.base_url' => 'http://dummy-ollama.local',
            'services.ollama.model' => 'dummy-model',
        ]);

        $selected = (new OllamaService())->pickTopicForRegion('Bandung', 'bandung');

        $this->assertArrayHasKey('topic', $selected);
        $this->assertArrayHasKey('focus', $selected);
        $this->assertStringContainsString('K3 Listrik', (string) $selected['topic']);
    }

    public function test_presentation_grouping_keeps_every_service_once(): void
    {
        $service = app(SchemeService::class);
        $groups = $service->groupedForPresentation();

        $this->assertSame(['maintenance-pengukuran', 'instalasi-proteksi', 'otomasi-industri', 'k3-listrik'], array_keys($groups));
        $this->assertCount(60, array_merge(...array_values($groups)));
        $this->assertGreaterThan(0, count($groups['k3-listrik']));
        $this->assertGreaterThan(0, count($groups['instalasi-proteksi']));
        $this->assertGreaterThan(0, count($groups['otomasi-industri']));
        $this->assertGreaterThan(0, count($groups['maintenance-pengukuran']));

        // Core safety names land in the K3 Listrik bucket.
        $this->assertArrayHasKey('pelatihan-teknisi-k3-listrik', $groups['k3-listrik']);
        $this->assertArrayHasKey('arc-flash-k3-listrik', $groups['k3-listrik']);

        // Installation topics are grouped under Instalasi & Proteksi.
        $this->assertArrayHasKey('panel-listrik-dan-switchgear-system', $groups['instalasi-proteksi']);

        // Automation topics stay in Otomasi Industri.
        $this->assertArrayHasKey('plc', $groups['otomasi-industri']);
        $this->assertArrayHasKey('scada', $groups['otomasi-industri']);
    }
}
