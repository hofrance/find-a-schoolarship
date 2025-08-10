<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Detection;
use League\Csv\Reader;
use League\Csv\Statement;
use Illuminate\Support\Str;

class ImportDetections extends Command
{
    protected $signature = 'detections:import {csv=../web/detections.csv} {--truncate}';
    protected $description = 'Import detections from CSV into SQLite (upsert by item_url)';

    public function handle(): int
    {
        $csvPath = base_path($this->argument('csv'));
        if (!file_exists($csvPath)) {
            $this->error("CSV not found: $csvPath");
            return 1;
        }

        if ($this->option('truncate')) {
            Detection::truncate();
        }

        $csv = Reader::createFromPath($csvPath, 'r');
        $csv->setHeaderOffset(0);
        $stmt = Statement::create();
        $records = $stmt->process($csv);

        $count = 0;
        foreach ($records as $r) {
            $row = array_map(fn($v) => is_string($v) ? trim($v) : $v, $r);
            if (empty($row['item_url']) || empty($row['title'])) {
                continue;
            }
            // Upsert by item_url
            Detection::updateOrCreate(
                ['item_url' => Str::limit($row['item_url'], 2048, '')],
                [
                    'source_name' => $row['source_name'] ?? null,
                    'title' => Str::limit($row['title'] ?? '', 1024, ''),
                    'country' => $row['country'] ?? null,
                    'level' => $row['level'] ?? null,
                    'language' => $row['language'] ?? null,
                    'score' => (int)($row['score'] ?? 0),
                    'deadline' => $row['deadline'] ?: null,
                    'amount' => $row['amount'] ?? null,
                    'first_seen' => $row['first_seen'] ?: null,
                    'last_seen' => $row['last_seen'] ?: null,
                    'source_id' => $row['source_id'] ?? null,
                    'provider' => $row['provider'] ?? null,
                    'category' => $row['category'] ?? null,
                    'funding_type' => $row['funding_type'] ?? null,
                    'region' => $row['region'] ?? null,
                    'fields' => $row['fields'] ?? null,
                    'tags' => $row['tags'] ?? null,
                    'source_url' => $row['source_url'] ?? null,
                    'summary' => $row['summary'] ?? null,
                ]
            );
            $count++;
        }

        $this->info("Imported/updated $count rows from $csvPath");
        return 0;
    }
}
