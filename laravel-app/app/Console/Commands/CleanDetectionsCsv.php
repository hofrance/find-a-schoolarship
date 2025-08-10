<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use League\Csv\Reader;
use League\Csv\Statement;
use Carbon\Carbon;

class CleanDetectionsCsv extends Command
{
    protected $signature = 'detections:clean-csv {csv=../data/detections.csv} {--min-score=5} {--drop-expired} {--output=}';
    protected $description = 'Épure data/detections.csv: dédoublonnage, normalisation deadline, tri, puis réécriture du CSV';

    public function handle(): int
    {
        $in = base_path($this->argument('csv'));
        if (!file_exists($in)) {
            $this->error("CSV not found: $in");
            return 1;
        }
        $out = $this->option('output') ? base_path($this->option('output')) : $in;
        $minScore = (int)$this->option('min-score');
        $dropExpired = (bool)$this->option('drop-expired');

        $csv = Reader::createFromPath($in, 'r');
        $csv->setHeaderOffset(0);
        $records = Statement::create()->process($csv);

        $rows = [];
        $headers = null;
        foreach ($records as $r) {
            $row = [];
            foreach ($r as $k => $v) { $row[$k] = is_string($v) ? trim($v) : $v; }
            if ($headers === null) { $headers = array_keys($row); }
            $title = $row['title'] ?? '';
            $url = $row['item_url'] ?? '';
            if (!$title || !$url) { continue; }
            $score = (int)($row['score'] ?? 0);
            if ($score < $minScore) { continue; }

            // Normalize deadline to YYYY-MM-DD if possible
            $dl = trim((string)($row['deadline'] ?? ''));
            if ($dl !== '') {
                $iso = $this->toIsoDate($dl);
                $row['deadline'] = $iso ?: null;
            } else {
                $row['deadline'] = null;
            }
            if ($dropExpired && $row['deadline']) {
                try {
                    if (Carbon::parse($row['deadline'])->lt(Carbon::today())) { continue; }
                } catch (\Throwable $e) {}
            }

            // Canonicalize URL for dedupe key
            $key = $this->canonicalizeUrl($url);
            if (!isset($rows[$key]) || ((int)($row['score'] ?? 0)) > ((int)($rows[$key]['score'] ?? 0))) {
                $row['item_url'] = $key; // store canonicalized URL
                $rows[$key] = $row;
            }
        }

        // Sort: deadline asc (nulls last), then score desc
        $list = array_values($rows);
        usort($list, function($a, $b) {
            $da = $a['deadline'] ?: '9999-12-31';
            $db = $b['deadline'] ?: '9999-12-31';
            if ($da === $db) {
                return ((int)($b['score'] ?? 0)) <=> ((int)($a['score'] ?? 0));
            }
            return strcmp($da, $db);
        });

        if (!$headers) {
            $this->error('No headers found.');
            return 1;
        }

        // Write atomically
        $tmp = $out . '.tmp';
        $fp = fopen($tmp, 'w');
        if (!$fp) { $this->error('Cannot write temp file'); return 1; }
        fputcsv($fp, $headers);
        foreach ($list as $r) {
            $row = [];
            foreach ($headers as $h) { $row[] = $r[$h] ?? ''; }
            fputcsv($fp, $row);
        }
        fclose($fp);
        if (!rename($tmp, $out)) {
            $this->error('Failed to replace output file');
            return 1;
        }

        $this->info('CSV cleaned: '.count($list).' rows -> '.$out);
        return 0;
    }

    private function toIsoDate(string $val): ?string
    {
        $s = trim($val);
        if ($s === '') return null;
        // Already ISO
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $s)) { return $s; }
        // Try with Carbon parser
        try {
            return Carbon::parse($s)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function canonicalizeUrl(string $url): string
    {
        $u = parse_url(trim($url));
        if (!$u || empty($u['host'])) return trim($url);
        $scheme = isset($u['scheme']) ? strtolower($u['scheme']) : 'http';
        $host = strtolower($u['host']);
        $path = isset($u['path']) && $u['path'] !== '' ? $u['path'] : '/';
        // Drop common index files
        foreach (['/index.html','/index.htm','/index.php','/index.asp'] as $suf) {
            if (str_ends_with(strtolower($path), $suf)) { $path = substr($path, 0, -strlen($suf)) ?: '/'; break; }
        }
        // Collapse duplicate slashes
        $path = preg_replace('#//+#','/',$path);
        if (strlen($path) > 1 && str_ends_with($path, '/')) { $path = substr($path, 0, -1); }
        // Drop fragment
        // Filter query: remove tracking params and sort
        $query = '';
        if (!empty($u['query'])) {
            parse_str($u['query'], $q);
            $q = array_filter($q, function($v, $k){
                $kp = strtolower($k);
                if ($v === '' || $v === null) return false;
                return !in_array($kp, ['utm_source','utm_medium','utm_campaign','utm_term','utm_content','gclid','fbclid','yclid','mc_cid','mc_eid','pk_campaign','pk_kwd','ref']);
            }, ARRAY_FILTER_USE_BOTH);
            if (!empty($q)) {
                ksort($q);
                $query = http_build_query($q);
            }
        }
        $port = isset($u['port']) ? ':' . $u['port'] : '';
        return $scheme . '://' . $host . $port . $path . ($query ? '?' . $query : '');
    }
}
