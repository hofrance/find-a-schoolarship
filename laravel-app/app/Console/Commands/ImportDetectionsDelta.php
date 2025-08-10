<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Detection;
use League\Csv\Reader;
use League\Csv\Statement;
use Illuminate\Support\Str;

class ImportDetectionsDelta extends Command
{
    protected $signature = 'detections:import-delta {csv=../data/detections.csv}';
    protected $description = 'Import only new or updated rows by item_url from CSV into SQLite';

    public function handle(): int
    {
        $csvPath = base_path($this->argument('csv'));
        if (!file_exists($csvPath)) {
            $this->error("CSV not found: $csvPath");
            return 1;
        }

        // Load existing item_urls into memory for quick membership checks
        $existing = Detection::pluck('item_url')->all();
        $have = array_fill_keys($existing, true);

        $csv = Reader::createFromPath($csvPath, 'r');
        $csv->setHeaderOffset(0);
        $records = Statement::create()->process($csv);

        $imported = 0; $updated = 0; $skipped = 0;
        foreach ($records as $r) {
            $row = array_map(fn($v) => is_string($v) ? trim($v) : $v, $r);
            $url = $row['item_url'] ?? '';
            $title = $row['title'] ?? '';
            if (!$url || !$title) { $skipped++; continue; }

            // If not present, create; if present, update some mutable fields if changed
            if (!isset($have[$url])) {
                Detection::create([
                    'item_url' => Str::limit($url, 2048, ''),
                    'title' => Str::limit($title, 1024, ''),
                    'source_name' => $row['source_name'] ?? null,
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
                ]);
                $have[$url] = true;
                $imported++;
            } else {
                $det = Detection::where('item_url', $url)->first();
                if ($det) {
                    $changed = false;
                    $mutable = [
                        'title','source_name','country','level','language','score','deadline','amount','first_seen','last_seen',
                        'source_id','provider','category','funding_type','region','fields','tags','source_url','summary'
                    ];
                    $updates = [];
                    foreach ($mutable as $k) {
                        $new = $row[$k] ?? null;
                        $old = $det->$k;
                        if ($new === '') { $new = null; }
                        if ($old != $new) { $updates[$k] = $new; $changed = true; }
                    }
                    if ($changed) {
                        // enforce limits
                        if (isset($updates['title'])) { $updates['title'] = Str::limit($updates['title'] ?? '', 1024, ''); }
                        $det->fill($updates);
                        $det->save();
                        $updated++;
                    } else {
                        $skipped++;
                    }
                }
            }
        }

        $this->info("Delta import done. new=$imported updated=$updated skipped=$skipped from $csvPath");
        return 0;
    }
}
