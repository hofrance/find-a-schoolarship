<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Detection;
use League\Csv\Reader;
use League\Csv\Statement;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

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

        try {
            $csv = Reader::createFromPath($csvPath, 'r');
        } catch (\Throwable $e) {
            $this->error('Cannot open CSV: '.$e->getMessage());
            return 1;
        }
        $csv->setHeaderOffset(0);

        $stmt = Statement::create();
        try {
            $records = $stmt->process($csv);
        } catch (\Throwable $e) {
            $this->error('CSV parse error: '.$e->getMessage());
            return 1;
        }

        $chunk = [];
        $chunkMap = []; // line index map for error reporting
        $chunkSize = 1000;
        $now = Carbon::now();
        $total = 0; $batches = 0; $skipped = 0; $rowIdx = 1; // header at 0

        $flushChunk = function() use (&$chunk, &$chunkMap, &$batches, &$skipped) {
            if (empty($chunk)) { return; }
            try {
                Detection::upsert(
                    $chunk,
                    ['item_url'],
                    ['title','source_name','country','level','language','score','deadline','amount','first_seen','last_seen','source_id','provider','category','funding_type','region','fields','tags','source_url','summary','updated_at']
                );
                $batches++;
            } catch (\Throwable $e) {
                // Fallback per‑row to continue on error, and log the offending line
                foreach ($chunk as $i => $row) {
                    try {
                        Detection::upsert([
                            $row
                        ], ['item_url'], ['title','source_name','country','level','language','score','deadline','amount','first_seen','last_seen','source_id','provider','category','funding_type','region','fields','tags','source_url','summary','updated_at']);
                    } catch (\Throwable $inner) {
                        $line = $chunkMap[$i] ?? 'unknown';
                        $msg = "Skip line $line: ".$inner->getMessage();
                        Log::warning('[detections:import] '.$msg);
                        // Show concise output too
                        $this->warn($msg);
                        $skipped++;
                    }
                }
                $batches++;
            }
            $chunk = []; $chunkMap = [];
        };

        $push = function(array $row, int $line) use (&$chunk, &$chunkMap, $chunkSize, $now, &$total, &$flushChunk, &$skipped) {
            $get = fn($k) => isset($row[$k]) && $row[$k] !== '' ? trim((string)$row[$k]) : null;
            $url = $get('item_url');
            $title = $get('title');
            if (!$url || !$title) {
                $skipped++; return; // skip invalid
            }
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
            $chunk[] = $attrs; $chunkMap[] = $line; $total++;
            if (count($chunk) >= $chunkSize) { $flushChunk(); }
        };

        foreach ($records as $idx => $r) {
            $row = [];
            foreach ($r as $k => $v) { $row[$k] = is_string($v) ? trim($v) : $v; }
            $rowIdx = $idx + 1; // considering header offset
            try {
                $push($row, $rowIdx);
            } catch (\Throwable $e) {
                $this->warn('Skip line '.$rowIdx.': '.$e->getMessage());
                $skipped++;
            }
        }
        $flushChunk();

        $this->info("Upserted $total rows in $batches batch(es) from $csvPath; skipped $skipped invalid row(s)");
        return 0;
    }
}
