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
        'k3listrik' => 'k3listrik.json',
    ];

    protected const SOURCE_IMAGES = [
        'k3listrik' => '/images/home/sumber-teknisi.jpg',
    ];

    /**
     * Descriptive Indonesian alt text specific to each category image.
     */
    protected const SOURCE_IMAGE_ALTS = [
        'k3listrik' => 'Teknisi K3 Listrik dengan helm keselamatan di dekat panel listrik dan instalasi kelistrikan industri',
    ];

    /**
     * Homepage presentation groupings for the unified catalog. Display-only
     * buckets matched against service names; names, slugs and records stay
     * untouched. Evaluation order matters: verbs-and-tools, then installation,
     * then automation, then safety/roles, anything unmatched falls to
     * Maintenance & Pengukuran.
     */
    protected const PRESENTATION_GROUPS = [
        'maintenance-pengukuran' => ['maintenance', 'pemeliharaan', 'pengoperasian', 'testing', 'commissioning', 'troubleshooting', 'multimeter', 'alat ukur'],
        'instalasi-proteksi' => ['instalasi', 'inslatasi', 'proteksi', 'grounding', 'wiring', 'distribution', 'panel', 'switchgear', 'switchboard', 'circuit breaker', 'short circuit', 'overcurrent', 'transformator', 'penerangan', 'pju', 'sel surya', 'air conditioning', 'gedung', 'arus kuat', 'komponen listrik'],
        'otomasi-industri' => ['plc', 'scada', 'otomasi', 'elektronika', 'elektro', 'arus lemah'],
        'k3-listrik' => ['k3', 'osha', 'arc flash', 'arc blast', 'shock', 'kebakaran', 'bahaya', 'alat pelindung', 'helm', 'sarung tangan', 'gfci', 'inspektur'],
    ];

    /**
     * Role keywords for the group fallback (Maintenance & Pengukuran).
     */
    protected const PRESENTATION_ROLES = ['engineer', 'technician', 'teknisi', 'operator', 'supervisor', 'manager', 'praktisi', 'pemula'];

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

    /**
     * Group the unified catalog into the four homepage presentation buckets.
     * Purely presentational: keyed by each program's slug, order preserved.
     *
     * @return array<string, array<string, array>> bucket => slug => program
     */
    public function groupedForPresentation(): array
    {
        $groups = [
            'maintenance-pengukuran' => [],
            'instalasi-proteksi' => [],
            'otomasi-industri' => [],
            'k3-listrik' => [],
        ];

        foreach ($this->load() as $slug => $program) {
            $t = ' ' . mb_strtolower((string) $program['title']) . ' ';

            if (self::matchesAny(self::PRESENTATION_GROUPS['maintenance-pengukuran'], $t)) {
                $bucket = 'maintenance-pengukuran';
            } elseif (self::matchesAny(self::PRESENTATION_GROUPS['instalasi-proteksi'], $t)) {
                $bucket = 'instalasi-proteksi';
            } elseif (self::matchesAny(self::PRESENTATION_GROUPS['otomasi-industri'], $t)) {
                $bucket = 'otomasi-industri';
            } elseif (self::matchesAny(self::PRESENTATION_GROUPS['k3-listrik'], $t)) {
                $bucket = 'k3-listrik';
            } elseif (self::matchesAny(self::PRESENTATION_ROLES, $t)) {
                $bucket = 'maintenance-pengukuran';
            } else {
                $bucket = 'maintenance-pengukuran';
            }

            $groups[$bucket][$slug] = $program;
        }

        return $groups;
    }

    /**
     * Substring match on the padded lowercase title.
     *
     * @param array<int, string> $keywords
     */
    protected static function matchesAny(array $keywords, string $lowercasedTitle): bool
    {
        foreach ($keywords as $keyword) {
            if (str_contains($lowercasedTitle, ' ' . $keyword)) {
                return true;
            }
        }

        return false;
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
        // Support the k3listrik.json catalog, which is a top-level list of service names.
        if (array_is_list($decoded) && isset($decoded[0]) && is_string($decoded[0])) {
            $serviceNames = array_values(array_filter(array_map('trim', $decoded), static fn (string $v): bool => $v !== ''));
            $decoded = [
                'title' => 'K3 Listrik',
                'schemes' => array_map(
                    static fn (string $name): array => ['name' => $name, 'lsp' => null, 'units' => []],
                    $serviceNames
                ),
            ];
        }

        if (!isset($decoded['schemes']) || !is_array($decoded['schemes'])) {
            throw new \RuntimeException('Scheme source ' . $sourceFile . ' does not contain the expected "schemes" structure.');
        }

        $sourceTitle = trim((string) ($decoded['title'] ?? 'K3 Listrik'));

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
            'image_url' => self::SOURCE_IMAGES[$sourceKey] ?? '/images/home/sumber-teknisi.jpg',
            'image_alt' => self::SOURCE_IMAGE_ALTS[$sourceKey] ?? 'Pelatihan K3 Listrik dan kelistrikan industri',
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
            'image_url' => self::SOURCE_IMAGES[$sourceKey] ?? '/images/home/sumber-teknisi.jpg',
            'image_alt' => self::SOURCE_IMAGE_ALTS[$sourceKey] ?? 'Pelatihan K3 Listrik dan kelistrikan industri',
            'category' => 'training-service',
            'category_label' => 'Layanan Pelatihan K3 Listrik',
            'sector' => 'k3-listrik',
            'level' => $lsp ?? 'Layanan K3 Listrik',
            'title' => $name,
            'badge' => $lsp ?? 'Layanan K3 Listrik',
            'meta_title' => $name . ' | K3 Listrik',
            'meta_description' => 'Layanan pelatihan K3 Listrik ' . $name . '. Rincian program dikonfirmasi melalui konsultasi.',
            'hero_heading' => 'Pelatihan K3 Listrik',
            'hero_highlight' => $name,
            'hero_subheading' => 'Informasi layanan pelatihan K3 Listrik ' . $name . '; rincian penyelenggaraan dikonfirmasi melalui konsultasi.',
            'wa_message' => 'Halo, saya ingin konsultasi mengenai ' . $name . '.',
            'introduction' => 'Layanan ' . $name . ' merupakan bagian dari program pelatihan K3 Listrik. Rincian kurikulum dan persyaratan penyelenggaraan dikonfirmasi melalui konsultasi.',
            'target_audience' => [
                'Tenaga kerja, pengawas, teknisi, operator, dan profesional yang bekerja pada instalasi kelistrikan dan sistem tenaga listrik; kecocokan latar belakang dikonfirmasi melalui konsultasi.',
            ],
            'roles_and_responsibilities' => [
                [
                    'title' => 'Kompetensi Skema',
                    'description' => 'Rincian lingkup materi layanan ini dikonfirmasi melalui konsultasi.',
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
