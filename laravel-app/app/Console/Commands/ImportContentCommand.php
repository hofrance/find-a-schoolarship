<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\Career;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ImportContentCommand extends Command
{
    protected $signature = 'content:import
        {--path= : Dossier racine des fichiers JSON (par défaut: storage/app/import)}
        {--only=* : Importer uniquement certains types (articles, careers)}
        {--dry-run : Simuler sans écrire en base}';

    protected $description = 'Importer des articles et des métiers depuis des fichiers JSON';

    public function handle(): int
    {
        $base = $this->option('path') ?: storage_path('app/import');
        $only = collect((array) $this->option('only'))
            ->filter()
            ->map(fn($v) => strtolower(trim((string)$v)))
            ->values();

        $this->info("Import depuis: {$base}");
        if (!File::exists($base)) {
            File::makeDirectory($base, 0755, true);
            File::makeDirectory($base.'/articles', 0755, true);
            File::makeDirectory($base.'/careers', 0755, true);
            $this->warn('Dossier import créé. Placez vos JSON dans articles/ et careers/.');
        }

        $doArticles = $only->isEmpty() || $only->contains('articles');
        $doCareers  = $only->isEmpty() || $only->contains('careers');
        $dryRun = (bool) $this->option('dry-run');

        $totalCreated = 0; $totalUpdated = 0; $errors = 0;

        if ($doArticles) {
            $this->line('— Import des articles');
            [$created, $updated, $err] = $this->importArticles($base.'/articles', $dryRun);
            $totalCreated += $created; $totalUpdated += $updated; $errors += $err;
        }

        if ($doCareers) {
            $this->line('— Import des métiers');
            [$created, $updated, $err] = $this->importCareers($base.'/careers', $dryRun);
            $totalCreated += $created; $totalUpdated += $updated; $errors += $err;
        }

        $this->info("Terminé: +{$totalCreated} créés, ~{$totalUpdated} mis à jour, {$errors} erreur(s)");
        if ($dryRun) { $this->comment('Mode simulation (dry-run) activé: aucune écriture en base.'); }
        return $errors > 0 ? self::FAILURE : self::SUCCESS;
    }

    /**
     * @return array{int,int,int} [created, updated, errors]
     */
    protected function importArticles(string $dir, bool $dryRun): array
    {
        if (!File::exists($dir)) { File::makeDirectory($dir, 0755, true); }
        $files = collect(File::files($dir))->filter(fn($f) => Str::endsWith($f->getFilename(), '.json'));
        $created = 0; $updated = 0; $errors = 0;

        foreach ($files as $file) {
            try {
                $data = json_decode(File::get($file->getRealPath()), true, 512, JSON_THROW_ON_ERROR);
                $items = $this->normalizeItems($data);
                foreach ($items as $item) {
                    $payload = $this->mapArticle($item);
                    if (!$payload) { $errors++; continue; }

                    [$wasCreated, $wasUpdated] = $this->upsertArticle($payload, $dryRun);
                    $created += $wasCreated; $updated += $wasUpdated;
                }
                $this->info("✓ Articles: {$file->getFilename()} ({".count($items)." items)");
            } catch (\Throwable $e) {
                $errors++; $this->error("✗ Articles: {$file->getFilename()} — ".$e->getMessage());
            }
        }
        return [$created, $updated, $errors];
    }

    /**
     * @return array{int,int,int} [created, updated, errors]
     */
    protected function importCareers(string $dir, bool $dryRun): array
    {
        if (!File::exists($dir)) { File::makeDirectory($dir, 0755, true); }
        $files = collect(File::files($dir))->filter(fn($f) => Str::endsWith($f->getFilename(), '.json'));
        $created = 0; $updated = 0; $errors = 0;

        foreach ($files as $file) {
            try {
                $data = json_decode(File::get($file->getRealPath()), true, 512, JSON_THROW_ON_ERROR);
                $items = $this->normalizeItems($data);
                foreach ($items as $item) {
                    $payload = $this->mapCareer($item);
                    if (!$payload) { $errors++; continue; }

                    [$wasCreated, $wasUpdated] = $this->upsertCareer($payload, $dryRun);
                    $created += $wasCreated; $updated += $wasUpdated;
                }
                $this->info("✓ Métiers: {$file->getFilename()} ({".count($items)." items)");
            } catch (\Throwable $e) {
                $errors++; $this->error("✗ Métiers: {$file->getFilename()} — ".$e->getMessage());
            }
        }
        return [$created, $updated, $errors];
    }

    protected function normalizeItems(mixed $data): array
    {
        if (is_array($data)) {
            // Fichier peut contenir un tableau d'items ou un objet avec clé racine
            if (Arr::isAssoc($data)) {
                // Essayer de trouver une clé plausible
                foreach (['items','data','articles','careers'] as $key) {
                    if (isset($data[$key]) && is_array($data[$key])) { return $data[$key]; }
                }
                return [$data];
            }
            return $data;
        }
        return [];
    }

    protected function mapArticle(array $item): ?array
    {
        $title = trim((string) ($item['title'] ?? ''));
        $slug  = trim((string) ($item['slug'] ?? ''));
        if ($title === '' && $slug === '') { $this->warn('Article ignoré: title/slug manquant'); return null; }
        if ($slug === '') { $slug = Str::slug($title); }

        $tags = $item['tags'] ?? [];
        if (is_string($tags)) { $tags = array_values(array_filter(array_map('trim', explode(',', $tags)))); }

        $authorId = null;
        if (!empty($item['author_email'])) {
            $user = User::where('email', $item['author_email'])->first();
            $authorId = $user?->id;
        }

        $publishedAt = $item['published_at'] ?? null;
        if ($publishedAt) { try { $publishedAt = Carbon::parse($publishedAt); } catch (\Throwable) { $publishedAt = null; } }

        return [
            'title' => $title,
            'slug' => $slug,
            'excerpt' => (string) ($item['excerpt'] ?? ''),
            'content' => (string) ($item['content'] ?? ''),
            'featured_image' => $item['featured_image'] ?? null,
            'category' => (string) ($item['category'] ?? 'orientation'),
            'tags' => $tags,
            'is_published' => (bool) ($item['is_published'] ?? true),
            'published_at' => $publishedAt,
            'locale' => (string) ($item['locale'] ?? app()->getLocale()),
            'author_id' => $authorId,
        ];
    }

    protected function mapCareer(array $item): ?array
    {
        $title = trim((string) ($item['title'] ?? ''));
        $slug  = trim((string) ($item['slug'] ?? ''));
        if ($title === '' && $slug === '') { $this->warn('Métier ignoré: title/slug manquant'); return null; }
        if ($slug === '') { $slug = Str::slug($title); }

        $education = $item['education_levels'] ?? [];
        if (is_string($education)) { $education = array_values(array_filter(array_map('trim', explode(',', $education)))); }
        $sectors = $item['sectors'] ?? [];
        if (is_string($sectors)) { $sectors = array_values(array_filter(array_map('trim', explode(',', $sectors)))); }

        return [
            'title' => $title,
            'slug' => $slug,
            'description' => (string) ($item['description'] ?? ''),
            'requirements' => (string) ($item['requirements'] ?? ''),
            'skills' => (string) ($item['skills'] ?? ''),
            'salary_range' => $item['salary_range'] ?? null,
            'education_levels' => $education,
            'sectors' => $sectors,
            'career_prospects' => (string) ($item['career_prospects'] ?? ''),
            'featured_image' => $item['featured_image'] ?? null,
            'is_featured' => (bool) ($item['is_featured'] ?? false),
            'locale' => (string) ($item['locale'] ?? app()->getLocale()),
        ];
    }

    /**
     * @return array{int,int} [created, updated]
     */
    protected function upsertArticle(array $payload, bool $dryRun): array
    {
        $now = now();
        if ($dryRun) { $this->line('· [dry] Article: '.$payload['slug']); return [0, 0]; }
        $existing = Article::where('slug', $payload['slug'])->first();
        if ($existing) {
            $existing->fill($payload);
            $existing->updated_at = $now;
            $existing->save();
            return [0, 1];
        }
        Article::create($payload + ['created_at' => $now, 'updated_at' => $now]);
        return [1, 0];
    }

    /**
     * @return array{int,int} [created, updated]
     */
    protected function upsertCareer(array $payload, bool $dryRun): array
    {
        $now = now();
        if ($dryRun) { $this->line('· [dry] Métier: '.$payload['slug']); return [0, 0]; }
        $existing = Career::where('slug', $payload['slug'])->first();
        if ($existing) {
            $existing->fill($payload);
            $existing->updated_at = $now;
            $existing->save();
            return [0, 1];
        }
        Career::create($payload + ['created_at' => $now, 'updated_at' => $now]);
        return [1, 0];
    }
}
