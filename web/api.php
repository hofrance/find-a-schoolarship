<?php
// Server-side API for DataTables to serve paginated/filterable scholarship rows from detections.csv
header('Content-Type: application/json; charset=utf-8');

$root = dirname(__DIR__);
$here = __DIR__;
$csvLocal = $here . '/detections.csv';
$csvData = $root . '/data/detections.csv';
$csvPath = file_exists($csvLocal) ? $csvLocal : $csvData;

$draw = isset($_POST['draw']) ? (int)$_POST['draw'] : 0;
$start = isset($_POST['start']) ? (int)$_POST['start'] : 0;
$length = isset($_POST['length']) ? (int)$_POST['length'] : 25;

// Order handling (support first 2 order instructions)
$order = [];
if (isset($_POST['order']) && is_array($_POST['order'])) {
    $order = $_POST['order'];
} else {
    // Fallback: try to rebuild from flat POST keys (order[0][column], order[0][dir])
    foreach ($_POST as $k => $v) {
        if (preg_match('/^order\[(\d+)\]\[(column|dir)\]$/', (string)$k, $m)) {
            $idx = (int)$m[1];
            $key = $m[2];
            if (!isset($order[$idx])) $order[$idx] = [];
            $order[$idx][$key] = $v;
        }
    }
}

// Filters from custom UI
function ensure_array($v) {
    if (!isset($v)) return [];
    if (is_array($v)) return array_values(array_filter($v, fn($x) => $x !== '' && $x !== null));
    if ($v === '') return [];
    return [$v];
}
$countries = ensure_array($_POST['countries'] ?? null);
$levels    = ensure_array($_POST['levels'] ?? null);
$langs     = ensure_array($_POST['langs'] ?? null);
// Use distinct names to avoid conflict with DataTables 'start' offset
$dateStart = isset($_POST['date_start']) ? trim((string)$_POST['date_start']) : '';
$dateEnd   = isset($_POST['date_end']) ? trim((string)$_POST['date_end']) : '';
$categories = ensure_array($_POST['categories'] ?? null);
$providers  = ensure_array($_POST['providers'] ?? null);
$fundingTypes = ensure_array($_POST['funding_types'] ?? null);
$regions    = ensure_array($_POST['regions'] ?? null);
$fieldsSel  = ensure_array($_POST['fields'] ?? null);

// Columns mapping to table header indices
$columns = [
    0 => 'source_name',
    1 => 'title',
    2 => 'country',
    3 => 'level',
    4 => 'language',
    5 => 'score',
    6 => 'deadline',
    7 => 'amount',
    8 => 'item_url',
];

$headers = [];
$rows = [];
if (file_exists($csvPath)) {
    if (($h = fopen($csvPath, 'r')) !== false) {
        $headers = fgetcsv($h);
        while (($data = fgetcsv($h)) !== false) {
            if ($headers && count($data) === count($headers)) {
                $rows[] = array_combine($headers, $data);
            }
        }
        fclose($h);
    }
}

$total = count($rows);

// Helper: fetch key safely
function getv($row, $key, $default = '') {
    return isset($row[$key]) ? (string)$row[$key] : $default;
}

// Apply filters
$filtered = array_values(array_filter($rows, function($r) use ($countries, $levels, $langs, $dateStart, $dateEnd, $categories, $providers, $fundingTypes, $regions, $fieldsSel) {
    $country = trim(getv($r, 'country'));
    if ($countries && !in_array($country, $countries, true)) return false;

    $level = trim(getv($r, 'level'));
    if ($levels && !in_array($level, $levels, true)) return false;

    $lang = trim(getv($r, 'language'));
    if ($langs) {
        $rowLangs = array_filter(array_map('trim', explode('|', $lang)));
        $ok = false;
        foreach ($langs as $l) { if (in_array($l, $rowLangs, true)) { $ok = true; break; } }
        if (!$ok) return false;
    }

    $deadline = trim(getv($r, 'deadline'));
    if (($dateStart !== '' || $dateEnd !== '')) {
        if ($deadline === '') return false;
        if ($dateStart !== '' && $deadline < $dateStart) return false;
        if ($dateEnd !== '' && $deadline > $dateEnd) return false;
    }

    // Optional extended filters (only if present)
    if ($categories) {
        $cat = trim(getv($r, 'category'));
        if ($cat === '' || !in_array($cat, $categories, true)) return false;
    }
    if ($providers) {
        $prov = trim(getv($r, 'provider'));
        if ($prov === '' || !in_array($prov, $providers, true)) return false;
    }
    if ($fundingTypes) {
        $ft = trim(getv($r, 'funding_type'));
        if ($ft === '' || !in_array($ft, $fundingTypes, true)) return false;
    }
    if ($regions) {
        $reg = trim(getv($r, 'region'));
        if ($reg === '' || !in_array($reg, $regions, true)) return false;
    }
    if ($fieldsSel) {
        $rowFields = array_filter(array_map('trim', explode('|', getv($r, 'fields'))));
        $okf = false;
        foreach ($fieldsSel as $f) { if (in_array($f, $rowFields, true)) { $okf = true; break; } }
        if (!$okf) return false;
    }

    return true;
}));

$filteredCount = count($filtered);

// Sorting
// Default: by deadline asc, then score desc
$sorters = [];
if (!empty($order)) {
    foreach ($order as $ord) {
        $ci = isset($ord['column']) ? (int)$ord['column'] : 0;
        $dir = (isset($ord['dir']) && strtolower($ord['dir']) === 'desc') ? -1 : 1;
        $colName = $columns[$ci] ?? 'deadline';
        $sorters[] = [$colName, $dir];
    }
} else {
    $sorters = [['deadline', 1], ['score', -1]];
}

usort($filtered, function($a, $b) use ($sorters) {
    foreach ($sorters as [$col, $dir]) {
        $av = isset($a[$col]) ? $a[$col] : '';
        $bv = isset($b[$col]) ? $b[$col] : '';
        if ($col === 'score') {
            $av = (float)$av; $bv = (float)$bv;
        } elseif ($col === 'deadline') {
            $av = $av !== '' ? $av : '9999-12-31';
            $bv = $bv !== '' ? $bv : '9999-12-31';
        }
        if ($av == $bv) continue;
        return ($av < $bv ? -1 : 1) * $dir;
    }
    return 0;
});

// Paging
if ($length < 0) { $length = 25; }
$pageRows = array_slice($filtered, $start, $length);

// Build output data objects
$data = array_map(function($r) {
    return [
        'source_name' => (string)($r['source_name'] ?? ''),
        'title'       => (string)($r['title'] ?? ''),
        'country'     => (string)($r['country'] ?? ''),
        'level'       => (string)($r['level'] ?? ''),
        'language'    => (string)($r['language'] ?? ''),
        'score'       => (string)($r['score'] ?? ''),
        'deadline'    => (string)($r['deadline'] ?? ''),
        'amount'      => (string)($r['amount'] ?? ''),
        'item_url'    => (string)($r['item_url'] ?? ''),
        // Extended (if needed on client later)
        'category'    => (string)($r['category'] ?? ''),
        'provider'    => (string)($r['provider'] ?? ''),
        'funding_type'=> (string)($r['funding_type'] ?? ''),
        'region'      => (string)($r['region'] ?? ''),
        'fields'      => (string)($r['fields'] ?? ''),
    ];
}, $pageRows);

echo json_encode([
    'draw' => $draw,
    'recordsTotal' => $total,
    'recordsFiltered' => $filteredCount,
    'data' => $data,
], JSON_UNESCAPED_UNICODE);
