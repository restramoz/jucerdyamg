<?php

namespace App\Services;

use Illuminate\Support\Str;

/**
 * Loads the five authoritative mining/energy scheme catalogs and exposes them
 * as one unified program catalog with source attribution.
 *
 * Source files are read-only: records are adapted, not rewritten. Names,
 * unit codes/names, lsp values and counts are preserved verbatim.
 */
class SchemeService
{
    /**
     * Authoritative source files, in a fixed display order.
     */
    protected const SOURCE_FILES = [
        'gppb' => 'schemes_gppb.json',
        'esdm' => 'schemes_esdm.json',
        'pertambangan_mineral_batubara_energi' => 'schemes_pertambangan_mineral_batubara_energi.json',
        'geo_mineral_batubara_energi' => 'schemes_geo_mineral_batubara_energi.json',
        'pertambangan_indonesia_mandiri' => 'schemes_pertambangan_indonesia_mandiri.json',
    ];

    protected const SOURCE_IMAGES = [
        'gppb' => 'https://images.unsplash.com/photo-1783428673892-209a796da566?auto=format&fit=crop&w=900&q=80',
        'esdm' => 'https://images.unsplash.com/photo-1786209273552-3b57dec47c94?auto=format&fit=crop&w=900&q=80',
        'pertambangan_mineral_batubara_energi' => 'https://images.unsplash.com/photo-1758143011530-e5b46487c4cc?auto=format&fit=crop&w=900&q=80',
        'geo_mineral_batubara_energi' => 'https://images.unsplash.com/photo-1580544859986-05322c53b1d4?auto=format&fit=crop&w=900&q=80',
        'pertambangan_indonesia_mandiri' => 'https://images.unsplash.com/photo-1781546441748-cdbefd22a586?auto=format&fit=crop&w=900&q=80',
    ];

    /**
     * Descriptive Indonesian alt text specific to each category image.
     */
    protected const SOURCE_IMAGE_ALTS = [
        'gppb' => 'Eksplorasi geotermal dan infrastruktur energi panas bumi: wilayah vulkanik dengan instalasi sumur dan sistem uap',
        'esdm' => 'Infrastruktur energi dan sumber daya mineral: fasilitas energi, jaringan, dan pengelolaan sumber daya mineral',
        'pertambangan_mineral_batubara_energi' => 'Penambangan terbuka batubara dan mineral dengan alat berat, excavator, dan truk pengangkut material',
        'geo_mineral_batubara_energi' => 'Formasi batuan geologi dan eksplorasi mineral dengan sampel batuan, singkapan, dan pengamatan lapangan',
        'pertambangan_indonesia_mandiri' => 'Operasi penambangan Indonesia dengan haul truck, fasilitas industri, dan area tambang terbuka',
    ];

    protected ?array $programs = null;

    protected ?array $sources = null;

    /**
     * Cached adapters for single files loaded ad-hoc.
     */
    protected array $cache = [];

    public function all(?string $source = null, array $filters = []): array
    {
        $programs = $this->load();

        if ($source !== null && $source !== '') {
            $filters['source'] = $source;
        }

        if ($filters === []) {
            return $programs;
        }

        return $this->filterLoaded($programs, $filters);
    }

    public function sources(): array
    {
        if ($this->sources === null) {
            $this->load();
        }

        return $this->sources ?? [];
    }

    public function getSource(string $key): ?array
    {
        return $this->sources()[$key] ?? null;
    }

    public function find(string $identifier): ?array
    {
        $all = $this->load();

        // Accept stable identifiers such as "source:slug".
        if (str_contains($identifier, ':')) {
            [$source, $slug] = explode(':', $identifier, 2);
            $slug = Str::slug($slug);
            if (isset($all[$slug]) && (($all[$slug]['source'] ?? null) === $source || ($all[$slug]['source_key'] ?? null) === $source)) {
                return $all[$slug];
            }
        }

        $key = Str::slug($identifier);

        return $all[$key] ?? null;
    }

    public function exists(string $identifier): bool
    {
        return $this->find($identifier) !== null;
    }

    public function bySource(string $source): array
    {
        return $this->all($source);
    }

    public function filterBySource(string $source): array
    {
        return $this->bySource($source);
    }

    public function schemesBySource(string $source): array
    {
        return $this->bySource($source);
    }

    public function filterByCategory(string $category): array
    {
        return $this->all($category);
    }

    public function filter(array $filters = []): array
    {
        return $this->all(null, $filters);
    }

    public function search(string $query): array
    {
        return $this->all(null, ['q' => $query]);
    }

    public function validateSelection(string $identifier): bool
    {
        return $this->exists($identifier);
    }

    public function sourceOf(string $slug): ?string
    {
        $record = $this->find($slug);

        return $record['source'] ?? $record['source_key'] ?? null;
    }

    protected function load(?string $path = null): array
    {
        // Ad-hoc single-file load, used by tests and validation tooling.
        if ($path !== null) {
            return $this->loadFile($path, 'file');
        }

        if ($this->programs !== null) {
            return $this->programs;
        }

        $programs = [];
        $sources = [];

        foreach (self::SOURCE_FILES as $key => $file) {
            $full = base_path($file);
            $raw = is_file($full) ? file_get_contents($full) : false;

            if ($raw === false) {
                throw new \RuntimeException('Unable to read scheme source file: ' . $file);
            }

            $decoded = json_decode($raw, true);

            if (!is_array($decoded)) {
                throw new \RuntimeException('Invalid JSON in scheme source file: ' . $file);
            }

            [$sourceMeta, $adapted] = $this->adaptSource($key, $file, $decoded);

            // Merge adapted schemes, preserving deterministic slug collisions
            // across the unified catalog (-2, -3, ...).
            foreach ($adapted as $scheme) {
                $name = trim((string) ($scheme['name'] ?? ''));
                if ($name === '') {
                    throw new \RuntimeException('Scheme source ' . $file . ' contains a scheme without a name.');
                }
                if (!isset($scheme['_slug'])) {
                    // Should never happen, but keeps the index deterministic.
                    $scheme['_slug'] = Str::slug($name);
                }
                $base = $scheme['_slug'];
                $slug = $base;
                $suffix = 2;
                while (isset($programs[$slug])) {
                    $slug = $base . '-' . $suffix;
                    $suffix++;
                }
                unset($scheme['_slug']);
                $program = $this->program($slug, $name, $scheme, $sourceMeta);
                $programs[$slug] = $program;
            }

            $sources[$key] = $sourceMeta;
        }

        // Sort nothing else; keep source-file order then source record order.
        $this->programs = $programs;
        $this->sources = $sources;

        return $this->programs;
    }

    /**
     * Load a single scheme file as a one-source catalog. Used for explicit
     * validation and tests.
     */
    protected function loadFile(string $path, string $fallbackSource): array
    {
        $default = $path === base_path('schemes.json');

        if ($default && $this->programs !== null) {
            return $this->programs;
        }

        $raw = is_file($path) ? file_get_contents($path) : false;

        if ($raw === false) {
            throw new \RuntimeException('Unable to read schemes file: ' . basename($path));
        }

        $decoded = json_decode($raw, true);

        if (!is_array($decoded)) {
            throw new \RuntimeException('Invalid JSON in schemes file: ' . basename($path));
        }

        $sourceKey = $fallbackSource;
        $sourceFile = basename($path);
        [$sourceMeta, $adapted] = $this->adaptSource($sourceKey, $sourceFile, $decoded);

        $programs = [];
        foreach ($adapted as $scheme) {
            $name = trim((string) ($scheme['name'] ?? ''));
            if ($name === '') {
                throw new \RuntimeException('Scheme source ' . $sourceFile . ' contains a scheme without a name.');
            }
            $base = Str::slug($name);
            $slug = $base;
            $suffix = 2;
            while (isset($programs[$slug])) {
                $slug = $base . '-' . $suffix;
                $suffix++;
            }
            $programs[$slug] = $this->program($slug, $name, $scheme, $sourceMeta);
        }

        if ($default) {
            $this->programs = $programs;
        }

        return $programs;
    }

    /**
     * Explicit adapter layer. Each source file is validated and normalized into
     * a common intermediate shape with source attribution attached.
     *
     * @return array{0:array,1:array} source metadata and adapted scheme list
     */
    protected function adaptSource(string $sourceKey, string $sourceFile, array $decoded): array
    {
        if (!isset($decoded['schemes']) || !is_array($decoded['schemes'])) {
            // Permit a top-level list as a final fallback, but keep strictness.
            throw new \RuntimeException('Scheme source ' . $sourceFile . ' does not contain the expected "schemes" structure.');
        }

        $sourceTitle = trim((string) ($decoded['title'] ?? $sourceFile));

        $adapted = [];
        foreach (array_values($decoded['schemes']) as $index => $scheme) {
            if (!is_array($scheme)) {
                throw new \RuntimeException('Scheme source ' . $sourceFile . ' contains a non-object scheme at index ' . $index . '.');
            }

            $adapted[] = [
                'name' => $scheme['name'] ?? null,
                'lsp' => $scheme['lsp'] ?? null,
                'units' => array_values($scheme['units'] ?? []),
                // Source attribution is mandatory for unified records.
                'source' => $sourceKey,
                'source_key' => $sourceKey,
                'source_title' => $sourceTitle,
                'source_file' => $sourceFile,
                'source_index' => $index,
                '_slug' => Str::slug((string) ($scheme['name'] ?? '')),
            ];
        }

        $unitCount = array_sum(array_map(
            static fn (array $s): int => count($s['units']),
            $adapted
        ));

        $meta = [
            'key' => $sourceKey,
            'source' => $sourceKey,
            'source_key' => $sourceKey,
            'title' => $sourceTitle,
            'source_title' => $sourceTitle,
            'file' => $sourceFile,
            'source_file' => $sourceFile,
            'count' => count($adapted),
            'scheme_count' => count($adapted),
            'unit_count' => $unitCount,
            'image_url' => self::SOURCE_IMAGES[$sourceKey] ?? 'https://images.unsplash.com/photo-1783428673892-209a796da566?auto=format&fit=crop&w=900&q=80',
            'image_alt' => self::SOURCE_IMAGE_ALTS[$sourceKey] ?? 'Aktivitas pertambangan dan energi',
        ];

        return [$meta, $adapted];
    }

    protected function filterLoaded(array $programs, array $filters): array
    {
        $result = [];

        foreach ($programs as $slug => $program) {
            if (!$this->matchesFilters($program, $filters)) {
                continue;
            }
            $result[$slug] = $program;
        }

        return $result;
    }

    protected function matchesFilters(array $program, array $filters): bool
    {
        foreach ($filters as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            if (in_array($key, ['source', 'source_key', 'source_name', 'source_title_check'], true)) {
                $source = (string) ($program['source'] ?? $program['source_key'] ?? '');
                if ($source !== (string) $value) {
                    return false;
                }
                continue;
            }

            if ($key === 'category' || $key === 'source_category') {
                if ((string) ($program['source'] ?? '') !== (string) $value && (string) ($program['source_title'] ?? '') !== (string) $value) {
                    return false;
                }
                continue;
            }

            if ($key === 'q' || $key === 'query' || $key === 'search') {
                $haystack = implode(' ', [
                    (string) ($program['title'] ?? ''),
                    (string) ($program['source_title'] ?? ''),
                    (string) ($program['slug'] ?? ''),
                ]);
                if (stripos($haystack, (string) $value) === false) {
                    return false;
                }
                continue;
            }

            if ($key === 'min_units') {
                if ((int) count($program['units'] ?? []) < (int) $value) {
                    return false;
                }
                continue;
            }

            if ($key === 'max_units') {
                if ((int) count($program['units'] ?? []) > (int) $value) {
                    return false;
                }
                continue;
            }

            if ($key === 'slug' || $key === 'id') {
                if ((string) ($program['slug'] ?? '') !== (string) $value && (string) ($program['stable_id'] ?? '') !== (string) $value) {
                    return false;
                }
                continue;
            }

            if (array_key_exists($key, $program) && $program[$key] !== $value) {
                // Loose comparison for scalar metadata.
                if ((string) $program[$key] !== (string) $value) {
                    return false;
                }
            }
        }

        return true;
    }

    protected function program(string $slug, string $name, array $scheme, array $sourceMeta): array
    {
        $units = array_values($scheme['units'] ?? []);
        $lsp = $scheme['lsp'] ?? null;
        $unitCount = count($units);
        $sourceKey = (string) ($scheme['source'] ?? $sourceMeta['key'] ?? '');
        $sourceTitle = (string) ($scheme['source_title'] ?? $sourceMeta['title'] ?? '');
        $sourceFile = (string) ($scheme['source_file'] ?? $sourceMeta['file'] ?? '');
        $stableId = $sourceKey . ':' . $slug;

        return [
            'id' => $stableId,
            'stable_id' => $stableId,
            'source' => $sourceKey,
            'source_key' => $sourceKey,
            'source_title' => $sourceTitle,
            'source_name' => $sourceTitle,
            'source_file' => $sourceFile,
            'source_index' => $scheme['source_index'] ?? null,
            'slug' => $slug,
            'image_url' => self::SOURCE_IMAGES[$sourceKey] ?? 'https://images.unsplash.com/photo-1783428673892-209a796da566?auto=format&fit=crop&w=900&q=80',
            'image_alt' => self::SOURCE_IMAGE_ALTS[$sourceKey] ?? 'Aktivitas pertambangan dan energi',
            'category' => 'scheme',
            'category_label' => 'Skema Kompetensi',
            'sector' => 'pertambangan-mineral-batubara-energi',
            'level' => $lsp ?? 'Skema Kompetensi',
            'title' => $name,
            'badge' => $lsp ?? 'Skema Kompetensi',
            'meta_title' => $name . ' | Skema Kompetensi',
            'meta_description' => 'Skema kompetensi ' . $name . ': ' . $unitCount . ' unit kompetensi. Rincian unit dan kode disertakan; penyelenggaraan dikonfirmasi melalui konsultasi.',
            'hero_heading' => 'Skema',
            'hero_highlight' => $name,
            'hero_subheading' => 'Daftar unit kompetensi skema ' . $name . ' tersedia pada halaman ini; rincian penyelenggaraan dikonfirmasi melalui konsultasi.',
            'wa_message' => 'Halo, saya ingin konsultasi mengenai skema ' . $name . '.',
            'introduction' => 'Skema kompetensi ' . $name . ' mencakup ' . $unitCount . ' unit kompetensi terkait pertambangan, mineral, batubara, dan energi. Daftar unit beserta kode unit disertakan pada halaman ini. Persyaratan penyelenggaraan dikonfirmasi melalui konsultasi.',
            'target_audience' => [
                'Tenaga kerja, pengawas, teknisi, dan badan usaha pada sektor pertambangan, mineral, batubara, dan energi; kecocokan latar belakang dikonfirmasi melalui konsultasi.',
            ],
            'roles_and_responsibilities' => [
                [
                    'title' => 'Kompetensi Skema',
                    'description' => 'Unit-unit berikut menjadi lingkup kompetensi yang dinilai pada skema ini.',
                ],
            ],
            'curriculum_topics' => array_map(
                static fn (array $unit): string => trim((string) ($unit['code'] ?? '')) . ' — ' . trim((string) ($unit['name'] ?? '')),
                $units
            ),
            'training_info' => [
                'jadwal' => ['label' => 'Jadwal Pelatihan', 'status' => 'Hubungi kami', 'detail' => 'Jadwal dikonfirmasi melalui konsultasi dengan penyelenggara.'],
                'durasi' => ['label' => 'Durasi Program', 'status' => 'Perlu verifikasi', 'detail' => 'Durasi dan moda penyelenggaraan dikonfirmasi bersama penyelenggara.'],
                'investasi' => ['label' => 'Investasi Biaya', 'status' => 'Hubungi kami', 'detail' => 'Biaya tidak dipublikasikan. Hubungi kami untuk penawaran resmi.'],
                'persyaratan' => ['label' => 'Persyaratan Peserta', 'status' => 'Perlu verifikasi', 'detail' => 'Persyaratan peserta dikonfirmasi ke penyelenggara skema.'],
                'otorisasi' => ['label' => 'Status Otorisasi', 'status' => 'Perlu verifikasi', 'detail' => 'Status skema kompetensi dikonfirmasi bersama LSP/penyelenggara terkait.'],
            ],
            'faqs' => [
                [
                    'question' => 'Berapa durasi pelaksanaan skema ini?',
                    'answer' => 'Durasi belum dipublikasikan dan perlu verifikasi melalui konsultasi dengan penyelenggara.',
                ],
                [
                    'question' => 'Bagaimana cara mendapatkan informasi biaya?',
                    'answer' => 'Biaya tidak dipublikasikan. Silakan hubungi kami melalui WhatsApp untuk informasi resmi.',
                ],
            ],
            'lsp' => $lsp,
            'units' => $units,
        ];
    }
}
