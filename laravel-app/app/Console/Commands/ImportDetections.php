<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Detection;
use League\Csv\Reader;
use League\Csv\Statement;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class ImportDetections extends Command
{
    protected $signature = 'detections:import {csv=../data/detections.csv} {--truncate}';
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

        $chunk = [];
        $chunkSize = 1000;
        $now = Carbon::now();
        $total = 0; $batches = 0;

        $push = function(array $row) use (&$chunk, $chunkSize, $now, &$total, &$batches) {
            $get = fn($k) => isset($row[$k]) && $row[$k] !== '' ? trim((string)$row[$k]) : null;
            $url = $get('item_url');
            $title = $get('title');
            if (!$url || !$title) { return; }
            $attrs = [
                'item_url'    => Str::limit($url, 2048, ''),
                'title'       => Str::limit($title, 1024, ''),
                'source_name' => $get('source_name') ? Str::limit($get('source_name'), 255, '') : null,
                'country'     => $get('country') ? Str::limit($get('country'), 128, '') : null,
                'level'       => $get('level') ? Str::limit($get('level'), 128, '') : null,
                'language'    => $get('language') ? Str::limit($get('language'), 64, '') : null,
                'score'       => (int)($get('score') ?? 0),
                'deadline'    => $get('deadline') ?: null,
                'amount'      => $get('amount') ? Str::limit($get('amount'), 128, '') : null,
                'first_seen'  => $get('first_seen') ?: null,
                'last_seen'   => $get('last_seen') ?: null,
                'source_id'   => $get('source_id') ? Str::limit($get('source_id'), 128, '') : null,
                'provider'    => $get('provider') ? Str::limit($get('provider'), 255, '') : null,
                'category'    => $get('category') ? Str::limit($get('category'), 128, '') : null,
                'funding_type'=> $get('funding_type') ? Str::limit($get('funding_type'), 128, '') : null,
                'region'      => $get('region') ? Str::limit($get('region'), 128, '') : null,
                'fields'      => $get('fields') ? Str::limit($get('fields'), 255, '') : null,
                'tags'        => $get('tags') ? Str::limit($get('tags'), 255, '') : null,
                'source_url'  => $get('source_url') ? Str::limit($get('source_url'), 2048, '') : null,
                'summary'     => $get('summary') ?? null,
                'created_at'  => $now,
                'updated_at'  => $now,
            ];
            $chunk[] = $attrs; $total++;
            if (count($chunk) >= $chunkSize) {
                Detection::upsert(
                    $chunk,
                    ['item_url'],
                    ['title','source_name','country','level','language','score','deadline','amount','first_seen','last_seen','source_id','provider','category','funding_type','region','fields','tags','source_url','summary','updated_at']
                );
                $chunk = []; $batches++;
            }
        };

        foreach ($records as $r) {
            $row = [];
            foreach ($r as $k => $v) { $row[$k] = is_string($v) ? trim($v) : $v; }
            $push($row);
        }
        if (!empty($chunk)) {
            Detection::upsert(
                $chunk,
                ['item_url'],
                ['title','source_name','country','level','language','score','deadline','amount','first_seen','last_seen','source_id','provider','category','funding_type','region','fields','tags','source_url','summary','updated_at']
            );
            $batches++;
        }

        $this->info("Upserted $total rows in $batches batch(es) from $csvPath");
        return 0;
    }
}
