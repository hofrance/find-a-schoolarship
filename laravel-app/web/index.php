<?php
// Simple PHP frontend to display detections.csv with a futuristic modern UI using CDN libs (TailwindCSS + Alpine.js + DataTables)
$root = dirname(__DIR__);
$here = __DIR__;
// Prefer local CSV in the same folder as index.php, fallback to data/detections.csv
$csvLocal = $here . "/detections.csv";
$csvData = $root . "/data/detections.csv";
$csvPath = file_exists($csvLocal) ? $csvLocal : $csvData;
$rows = [];
if (file_exists($csvPath)) {
    if (($h = fopen($csvPath, "r")) !== false) {
        $headers = fgetcsv($h);
        while (($data = fgetcsv($h)) !== false) {
            $row = array_combine($headers, $data);
            $rows[] = $row;
        }
        fclose($h);
    }
}
// Generate CSP nonce for inline scripts
$nonce = base64_encode(random_bytes(16));
// Build filter lists
$countries = [];
$levels = [];
$languages = [];
// New extended filter sets
$categories = [];
$providers = [];
$fundingTypes = [];
$regions = [];
$fieldsSet = [];
$latest = '';
foreach ($rows as $r) {
    $c = trim($r['country'] ?? ''); if ($c !== '') { $countries[$c] = true; }
    $lv = trim($r['level'] ?? ''); if ($lv !== '') { $levels[$lv] = true; }
    $lg = trim($r['language'] ?? ''); if ($lg !== '') { foreach (explode('|', $lg) as $ll) { $ll = trim($ll); if ($ll !== '') $languages[$ll] = true; } }
    // Extended fields
    $cat = trim($r['category'] ?? ''); if ($cat !== '') { $categories[$cat] = true; }
    $prov = trim($r['provider'] ?? ''); if ($prov !== '') { $providers[$prov] = true; }
    $ft = trim($r['funding_type'] ?? ''); if ($ft !== '') { $fundingTypes[$ft] = true; }
    $reg = trim($r['region'] ?? ''); if ($reg !== '') { $regions[$reg] = true; }
    $flds = trim($r['fields'] ?? ''); if ($flds !== '') { foreach (explode('|', $flds) as $f) { $f = trim($f); if ($f !== '') $fieldsSet[$f] = true; } }
    $ls = trim($r['last_seen'] ?? ($r['first_seen'] ?? ''));
    if ($ls !== '' && $ls > $latest) { $latest = $ls; }
}
ksort($countries); ksort($levels); ksort($languages);
ksort($categories); ksort($providers); ksort($fundingTypes); ksort($regions); ksort($fieldsSet);
$countries = array_keys($countries);
$levels = array_keys($levels);
$languages = array_keys($languages);
$categories = array_keys($categories);
$providers = array_keys($providers);
$fundingTypes = array_keys($fundingTypes);
$regions = array_keys($regions);
$fieldsSet = array_keys($fieldsSet);

// New: simple stats
$totalRows = count($rows);
$countriesCount = count($countries);

// Optional CSV download endpoint
if (isset($_GET['download']) && $_GET['download'] === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="detections.csv"');
    $out = fopen('php://output', 'w');
    $headersOut = [];
    if (!empty($rows)) {
        $headersOut = array_keys($rows[0]);
    } elseif (!empty($headers ?? [])) {
        $headersOut = $headers;
    }
    if (!empty($headersOut)) { fputcsv($out, $headersOut); }
    foreach ($rows as $r) {
        $line = [];
        foreach ($headersOut as $k) { $line[] = $r[$k] ?? ''; }
        fputcsv($out, $line);
    }
    fclose($out);
    exit;
}

// Security headers
$scriptSrc = "'self' https://cdn.jsdelivr.net https://cdn.datatables.net https://cdnjs.cloudflare.com https://cdn.tailwindcss.com 'nonce-{$nonce}'";
$styleSrc  = "'self' https://cdn.jsdelivr.net https://cdn.datatables.net https://fonts.googleapis.com https://cdnjs.cloudflare.com 'unsafe-inline'";
$csp = "default-src 'self'; object-src 'none'; base-uri 'none'; frame-ancestors 'none'; form-action 'self'; upgrade-insecure-requests; script-src $scriptSrc; style-src $styleSrc; img-src 'self' data:; font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com data:; connect-src 'self'";
header("Content-Security-Policy: $csp");
header('Referrer-Policy: strict-origin-when-cross-origin');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
header('Cross-Origin-Opener-Policy: same-origin');
header('Cross-Origin-Resource-Policy: same-origin');
header('Strict-Transport-Security: max-age=63072000; includeSubDomains; preload');

// Language selection
$lang = (isset($_GET['lang']) && in_array($_GET['lang'], ['fr','en'])) ? $_GET['lang'] : 'fr';
$T = [
  'fr' => [
    'app_title' => 'Biscuit Délicieux',
    'updated' => 'Dernière mise à jour',
    'tab_list' => 'Bourses',
    'tab_about' => 'À propos',
    'tab_orientation' => 'Orientation',
    'f_country' => 'Pays',
    'f_level' => 'Niveau',
    'f_lang' => 'Langue',
    'f_deadline_start' => 'Deadline (début)',
    'f_deadline_end' => 'Deadline (fin)',
    'th_source' => 'Source',
    'th_title' => 'Titre',
    'th_country' => 'Pays',
    'th_level' => 'Niveau',
    'th_lang' => 'Langue',
    'th_score' => 'Score',
    'th_deadline' => 'Deadline',
    'th_amount' => 'Montant',
    'th_link' => 'Lien',
    'open' => 'ouvrir',
    'about_h2' => 'À propos',
    'about_p1' => "Je suis <span class=\"font-semibold\">Richard BANKOUEZI</span>, <span class=\"font-semibold\">Architecte Cybersécurité & Digital Sentinel</span> à Paris. J’aide les équipes à <span class=\"text-cyan-300\">prévenir, détecter et répondre</span> aux menaces avec des résultats mesurables.",
    'about_p2' => "Expertises : SOC/SIEM, Réponse à incident (CERT/CSIRT), Threat Intelligence (MITRE ATT&CK), Sécurité Cloud, Conformité (RGPD).",
    'about_p3' => "Services : audits, pentest, réponse à incident <span class=\"text-cyan-300\">24/7</span>, formation, conformité et gestion des risques. <a class=\"text-cyan-300 underline\" href=\"https://richard-hofrance.com/en\" rel=\"noopener noreferrer\" target=\"_blank\">En savoir plus</a> — <a class=\"text-cyan-300 underline\" href=\"mailto:contact@richard-hofrance.com\">contact@richard-hofrance.com</a>.",
    'about_list1' => 'Audit & Pentest',
    'about_list2' => 'IR 24/7, SOC/SIEM',
    'about_list3' => 'Conformité, RGPD, Risques & Formation',
    'lang_switch' => 'English',
    'about_follow' => 'Suivez-moi',
    'stats_offers' => 'Offres',
    'stats_countries' => 'Pays',
    'reset_filters' => 'Réinitialiser',
    'download_csv' => 'Télécharger CSV',
    'th_category' => 'Catégorie',
    'th_provider' => 'Organisme',
    'th_funding_type' => 'Type de financement',
    'th_region' => 'Région',
    'th_fields' => 'Domaines',
    // Missing filter labels
    'f_category' => 'Catégorie',
    'f_provider' => 'Organisme',
    'f_funding_type' => 'Type de financement',
    'f_region' => 'Région',
    'f_fields' => 'Domaines',
    'f_page' => 'Lignes/page',
    // New a11y/search/pager labels
    'search' => 'Recherche',
    'search_placeholder' => 'Rechercher un titre, un organisme, un pays…',
    'pager_prev' => 'Précédent',
    'pager_next' => 'Suivant',
    // New mission/human touch
    'mission_title' => 'Notre mission',
    'mission_text' => "Ce projet aide les étudiant·e·s à trouver des opportunités de bourses. Parfois, il suffit d’avoir l’information au bon moment.",
    'mission_disclaimer' => "Projet indépendant, gratuit et sans partenariat — juste pour aider plus de monde.",
    'badge_community' => 'Communautaire',
    'badge_independent' => 'Indépendant',
    // CTA labels
    'cta_contribute' => 'Contribuer',
    'cta_contact' => 'Me contacter',
    'cta_github' => 'GitHub',
    // New navigation/filters labels
    'advanced_filters' => 'Filtres avancés',
    'active_filters' => 'Filtres actifs',
    'clear_all' => 'Tout effacer',
    'back_to_top' => 'Haut de page',
    'jump_to_filters' => 'Aller aux filtres',
    // Orientation content
    'orientation_h2' => "Orientation & Conseils",
    'orientation_p1' => "Trouver une bourse, c’est une méthode: cible, calendrier, dossier clair.",
    'orientation_steps' => [
      'Définir votre profil (pays, niveau, domaine, langues).',
      'Suivre les échéances et préparer les pièces à l’avance (CV, lettres, attestations).',
      'Personnaliser chaque candidature et vérifier l’éligibilité.',
      'Créer des alertes et partager avec votre réseau.',
    ],
    'orientation_links' => 'Ressources utiles',
    'orientation_cta' => 'Besoin d’orientation ? Me contacter',
  ],
  'en' => [
    'app_title' => 'Delicious Biscuit',
    'updated' => 'Last updated',
    'tab_list' => 'Scholarships',
    'tab_about' => 'About',
    'tab_orientation' => 'Guidance',
    'f_country' => 'Country',
    'f_level' => 'Level',
    'f_lang' => 'Language',
    'f_deadline_start' => 'Deadline (start)',
    'f_deadline_end' => 'Deadline (end)',
    'th_source' => 'Source',
    'th_title' => 'Title',
    'th_country' => 'Country',
    'th_level' => 'Level',
    'th_lang' => 'Language',
    'th_score' => 'Score',
    'th_deadline' => 'Deadline',
    'th_amount' => 'Amount',
    'th_link' => 'Link',
    'open' => 'open',
    'about_h2' => 'About',
    'about_p1' => "I'm <span class=\"font-semibold\">Richard BANKOUEZI</span>, a <span class=\"font-semibold\">Cybersecurity Architect & Digital Sentinel</span> in Paris. I help teams <span class=\"text-cyan-300\">prevent, detect, and respond</span> to threats with measurable outcomes.",
    'about_p2' => 'Expertise: SOC/SIEM, Incident Response (CERT/CSIRT), Threat Intelligence (MITRE ATT&CK), Cloud Security, Compliance (GDPR).',
    'about_p3' => "Services: audits, penetration testing, <span class=\"text-cyan-300\">24/7</span> incident response, training, compliance and risk management. <a class=\"text-cyan-300 underline\" href=\"https://richard-hofrance.com/en\" rel=\"noopener noreferrer\" target=\"_blank\">Learn more</a> — <a class=\"text-cyan-300 underline\" href=\"mailto:contact@richard-hofrance.com\">contact@richard-hofrance.com</a>.",
    'about_list1' => 'Audit & Penetration testing',
    'about_list2' => 'IR 24/7, SOC/SIEM',
    'about_list3' => 'Compliance, GDPR, Risk & Training',
    'lang_switch' => 'Français',
    'about_follow' => 'Follow me',
    'stats_offers' => 'Offers',
    'stats_countries' => 'Countries',
    'reset_filters' => 'Reset',
    'download_csv' => 'Download CSV',
    'th_category' => 'Category',
    'th_provider' => 'Provider',
    'th_funding_type' => 'Funding type',
    'th_region' => 'Region',
    'th_fields' => 'Fields',
    // Missing filter labels
    'f_category' => 'Category',
    'f_provider' => 'Provider',
    'f_funding_type' => 'Funding type',
    'f_region' => 'Region',
    'f_fields' => 'Fields',
    'f_page' => 'Rows/page',
    // New a11y/search/pager labels
    'search' => 'Search',
    'search_placeholder' => 'Search title, provider, country…',
    'pager_prev' => 'Previous',
    'pager_next' => 'Next',
    // New mission/human touch
    'mission_title' => 'Our mission',
    'mission_text' => 'This project helps students discover scholarship opportunities. Sometimes all you need is the right information at the right time.',
    'mission_disclaimer' => 'Independent, free, no partnerships — simply to help more people.',
    'badge_community' => 'Community',
    'badge_independent' => 'Independent',
    // CTA labels
    'cta_contribute' => 'Contribute',
    'cta_contact' => 'Contact me',
    'cta_github' => 'GitHub',
    // New navigation/filters labels
    'advanced_filters' => 'Advanced filters',
    'active_filters' => 'Active filters',
    'clear_all' => 'Clear all',
    'back_to_top' => 'Back to top',
    'jump_to_filters' => 'Jump to filters',
    // Orientation content
    'orientation_h2' => 'Guidance & Tips',
    'orientation_p1' => 'Finding scholarships is a method: target, timeline, crisp application.',
    'orientation_steps' => [
      'Define your profile (country, level, field, languages).',
      'Track deadlines and prepare documents early (resume, letters, proofs).',
      'Tailor each application and verify eligibility.',
      'Set alerts and share with your network.',
    ],
    'orientation_links' => 'Useful resources',
    'orientation_cta' => 'Need guidance? Contact me',
  ],
];
$L = $T[$lang];
$otherLang = $lang === 'fr' ? 'en' : 'fr';
$currentPath = strtok($_SERVER['REQUEST_URI'] ?? '/index.php', '?');
$langSwitchUrl = $currentPath . '?lang=' . $otherLang;
$downloadUrl = $currentPath . '?download=csv&lang=' . $lang;
$apiUrl = $currentPath . '?api=1';
?>
<!doctype html>
<html lang="<?php echo htmlspecialchars($lang); ?>" class="h-full theme-cyan" data-theme="dark">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?php echo htmlspecialchars($L['app_title'] ?? 'Scholarships'); ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
  <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.tailwindcss.css">
  <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
  <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <link rel="stylesheet" href="futuristic-style.css" />
  <style>
    /* Bridge existing classes to futuristic look */
    .neon { text-shadow: 0 0 5px var(--primary-light), 0 0 10px var(--primary-light), 0 0 20px var(--primary-light); }
    body { background-image: none; }
    table.dataTable tbody tr:hover { background-color: rgba(15,23,42,0.25) !important; }
    header nav a, header nav button { box-shadow: none; }
  </style>
</head>
<body class="h-full">
  <a href="#main-content" class="skip-link">Skip to content</a>
  <div class="min-h-full" x-data="{ tab: (localStorage.getItem('ui.tab')||'list') }" x-init="$watch('tab', t => localStorage.setItem('ui.tab', t))">
    <nav class="navbar" role="navigation" aria-label="Primary">
      <div class="nav-container d-flex justify-between align-center" style="display:flex;justify-content:space-between;align-items:center;padding:0 1rem;">
        <div class="nav-brand">
          <i class="fas fa-cookie-bite"></i>
          <span><?php echo htmlspecialchars($L['app_title'] ?? 'Delicious Biscuit'); ?></span>
        </div>
        <div class="nav-menu">
          <button id="btn-theme" class="btn btn-outline" title="Theme"><i class="fas fa-circle-half-stroke"></i><span class="hidden sm:inline">Theme</span></button>
          <a class="btn btn-outline" href="<?php echo htmlspecialchars($langSwitchUrl); ?>"><?php echo htmlspecialchars($L['lang_switch'] ?? 'Français'); ?></a>
          <button id="tab-list" class="btn nav-link" role="tab" :aria-selected="(tab==='list').toString()" :tabindex="tab==='list'? '0':'-1'" aria-controls="panel-list" :class="{ 'active': tab==='list' }" @click="tab='list'"><i class="fas fa-table"></i> <?php echo htmlspecialchars($L['tab_list']); ?></button>
          <button id="tab-orient" class="btn nav-link" role="tab" :aria-selected="(tab==='orientation').toString()" :tabindex="tab==='orientation'? '0':'-1'" aria-controls="panel-orientation" :class="{ 'active': tab==='orientation' }" @click="tab='orientation'"><i class="fas fa-compass"></i> <?php echo htmlspecialchars($L['tab_orientation']); ?></button>
          <button id="tab-about" class="btn nav-link" role="tab" :aria-selected="(tab==='about').toString()" :tabindex="tab==='about'? '0':'-1'" aria-controls="panel-about" :class="{ 'active': tab==='about' }" @click="tab='about'"><i class="fas fa-circle-info"></i> <?php echo htmlspecialchars($L['tab_about']); ?></button>
        </div>
      </div>
    </nav>

    <main id="main-content" class="p-6" role="main">
      <div id="filters-sentinel"></div>
      <header class="mb-6">
        <h1 class="section-title neon mb-2"><?php echo htmlspecialchars($L['app_title']); ?></h1>
        <p class="text-slate-400 flex flex-wrap items-center gap-2">
          <span>Sources: detections.csv · <?php echo htmlspecialchars($L['updated']); ?>: <span class="text-slate-300"><?php echo htmlspecialchars($latest ?: '—'); ?></span></span>
          <span class="badge badge-info" title="<?php echo htmlspecialchars($L['stats_offers']); ?>"><?php echo htmlspecialchars($L['stats_offers']); ?>: <strong class="ml-1"><?php echo (int)$totalRows; ?></strong></span>
          <span class="badge badge-success" title="<?php echo htmlspecialchars($L['stats_countries']); ?>"><?php echo htmlspecialchars($L['stats_countries']); ?>: <strong class="ml-1"><?php echo (int)$countriesCount; ?></strong></span>
          <a class="btn btn-outline ml-2" href="<?php echo htmlspecialchars($downloadUrl); ?>"><i class="fas fa-file-csv"></i> <?php echo htmlspecialchars($L['download_csv']); ?></a>
          <button id="btn-reset" class="btn btn-primary"><i class="fas fa-rotate"></i> <?php echo htmlspecialchars($L['reset_filters']); ?></button>
          <a class="btn btn-outline" href="#filters"><i class="fas fa-filter"></i> <?php echo htmlspecialchars($L['jump_to_filters']); ?></a>
        </p>
        <div class="glass mt-3" role="note" aria-label="<?php echo htmlspecialchars($L['mission_title']); ?>" style="padding:.75rem 1rem; display:flex; gap:.75rem; align-items:flex-start;">
          <i class="fas fa-heart" aria-hidden="true" style="color:#ef4444"></i>
          <div>
            <div class="font-semibold mb-1"><?php echo htmlspecialchars($L['mission_title']); ?></div>
            <p class="text-slate-300 p-muted" style="margin:0;">
              <?php echo $L['mission_text']; ?>
            </p>
            <div class="mt-2" style="display:flex; gap:.5rem; flex-wrap:wrap; align-items:center;">
              <span class="badge badge-success"><?php echo htmlspecialchars($L['badge_community']); ?></span>
              <span class="badge"><?php echo htmlspecialchars($L['badge_independent']); ?></span>
              <small class="text-slate-400"><?php echo htmlspecialchars($L['mission_disclaimer']); ?></small>
            </div>
            <div class="mt-2" style="display:flex; gap:.5rem; flex-wrap:wrap;">
              <a class="btn btn-primary" href="mailto:contact@richard-hofrance.com" aria-label="<?php echo htmlspecialchars($L['cta_contact']); ?>"><?php echo htmlspecialchars($L['cta_contribute']); ?></a>
              <a class="btn btn-outline" href="https://github.com/hofrance" target="_blank" rel="noopener noreferrer" aria-label="<?php echo htmlspecialchars($L['cta_github']); ?>"><?php echo htmlspecialchars($L['cta_github']); ?></a>
            </div>
          </div>
        </div>
      </header>

      <section x-show="tab==='list'" x-cloak role="tabpanel" id="panel-list" aria-labelledby="tab-list" x-transition.opacity.scale>
        <div class="card mb-4 filters-sticky" id="filters">
          <div class="d-flex justify-between align-center mb-2" style="display:flex;justify-content:space-between;align-items:center;gap:.5rem;flex-wrap:wrap;">
            <button id="toggle-advanced" class="btn btn-outline" aria-expanded="false"><i class="fas fa-sliders"></i> <?php echo htmlspecialchars($L['advanced_filters']); ?></button>
            <div class="d-flex align-center" style="display:flex;gap:.5rem;flex-wrap:wrap;">
              <span class="text-xs" style="color:var(--text-muted)"><?php echo htmlspecialchars($L['active_filters']); ?>:</span>
              <div id="chips" class="chips" aria-live="polite" aria-atomic="true" aria-label="<?php echo htmlspecialchars($L['active_filters']); ?>"></div>
              <button id="clear-all" class="btn btn-outline"><i class="fas fa-broom"></i> <?php echo htmlspecialchars($L['clear_all']); ?></button>
            </div>
          </div>
          <div class="filters-panel" style="display:flex;gap:1rem;flex-wrap:wrap;">
            <div style="min-width:200px;">
              <label class="text-xs mb-1"><?php echo htmlspecialchars($L['f_country']); ?></label>
              <select id="filter-country" multiple>
                <?php foreach ($countries as $v): ?>
                  <option value="<?php echo htmlspecialchars($v); ?>"><?php echo htmlspecialchars($v); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div style="min-width:200px;">
              <label class="text-xs mb-1"><?php echo htmlspecialchars($L['f_level']); ?></label>
              <select id="filter-level" multiple>
                <?php foreach ($levels as $v): ?>
                  <option value="<?php echo htmlspecialchars($v); ?>"><?php echo htmlspecialchars($v); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div style="min-width:200px;">
              <label class="text-xs mb-1"><?php echo htmlspecialchars($L['f_lang']); ?></label>
              <select id="filter-language" multiple>
                <?php foreach ($languages as $v): ?>
                  <option value="<?php echo htmlspecialchars($v); ?>"><?php echo htmlspecialchars($v); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div>
              <label class="text-xs mb-1"><?php echo htmlspecialchars($L['f_deadline_start']); ?></label>
              <input id="date-start" type="text" class="input-futuristic" placeholder="YYYY-MM-DD" />
            </div>
            <div>
              <label class="text-xs mb-1"><?php echo htmlspecialchars($L['f_deadline_end']); ?></label>
              <input id="date-end" type="text" class="input-futuristic" placeholder="YYYY-MM-DD" />
            </div>
            <div>
              <label class="text-xs mb-1"><?php echo htmlspecialchars($L['f_page']); ?></label>
              <select id="page-size" class="input-futuristic" style="width:auto;min-width:90px;">
                <option>10</option><option selected>25</option><option>50</option><option>100</option>
              </select>
            </div>
          </div>
          <div id="advanced-filters" class="collapsible hidden">
            <div class="filters-panel" style="display:flex;gap:1rem;flex-wrap:wrap;">
              <div style="min-width:200px;">
                <label class="text-xs mb-1"><?php echo htmlspecialchars($L['f_category']); ?></label>
                <select id="filter-category" multiple>
                  <?php foreach ($categories as $v): ?>
                    <option value="<?php echo htmlspecialchars($v); ?>"><?php echo htmlspecialchars($v); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div style="min-width:200px;">
                <label class="text-xs mb-1"><?php echo htmlspecialchars($L['f_provider']); ?></label>
                <select id="filter-provider" multiple>
                  <?php foreach ($providers as $v): ?>
                    <option value="<?php echo htmlspecialchars($v); ?>"><?php echo htmlspecialchars($v); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div style="min-width:200px;">
                <label class="text-xs mb-1"><?php echo htmlspecialchars($L['f_funding_type']); ?></label>
                <select id="filter-funding-type" multiple>
                  <?php foreach ($fundingTypes as $v): ?>
                    <option value="<?php echo htmlspecialchars($v); ?>"><?php echo htmlspecialchars($v); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div style="min-width:200px;">
                <label class="text-xs mb-1"><?php echo htmlspecialchars($L['f_region']); ?></label>
                <select id="filter-region" multiple>
                  <?php foreach ($regions as $v): ?>
                    <option value="<?php echo htmlspecialchars($v); ?>"><?php echo htmlspecialchars($v); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div style="min-width:220px;">
                <label class="text-xs mb-1"><?php echo htmlspecialchars($L['f_fields']); ?></label>
                <select id="filter-fields" multiple>
                  <?php foreach ($fieldsSet as $v): ?>
                    <option value="<?php echo htmlspecialchars($v); ?>"><?php echo htmlspecialchars($v); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
          </div>
          <div class="mt-3" role="search" aria-label="<?php echo htmlspecialchars($L['search']); ?>">
            <label for="global-search" class="text-xs mb-1"><?php echo htmlspecialchars($L['search']); ?></label>
            <div style="display:flex; gap:.5rem; align-items:center;">
              <input id="global-search" type="search" class="input-futuristic" placeholder="<?php echo htmlspecialchars($L['search_placeholder']); ?>" aria-label="<?php echo htmlspecialchars($L['search']); ?>" />
              <button id="clear-search" class="btn btn-outline" aria-label="Clear search"><i class="fas fa-xmark"></i></button>
            </div>
          </div>
        </div>

        <div class="table-container card">
          <table id="tbl" class="table-futuristic display text-sm" aria-describedby="info">
            <caption class="sr-only">Tableau des bourses avec filtres et pagination</caption>
            <thead>
              <tr>
                <th scope="col"><?php echo htmlspecialchars($L['th_source']); ?></th>
                <th scope="col"><?php echo htmlspecialchars($L['th_title']); ?></th>
                <th scope="col"><?php echo htmlspecialchars($L['th_country']); ?></th>
                <th scope="col"><?php echo htmlspecialchars($L['th_level']); ?></th>
                <th scope="col"><?php echo htmlspecialchars($L['th_lang']); ?></th>
                <th scope="col"><?php echo htmlspecialchars($L['th_score']); ?></th>
                <th scope="col"><?php echo htmlspecialchars($L['th_deadline']); ?></th>
                <th scope="col"><?php echo htmlspecialchars($L['th_amount']); ?></th>
                <th scope="col"><?php echo htmlspecialchars($L['th_category']); ?></th>
                <th scope="col"><?php echo htmlspecialchars($L['th_provider']); ?></th>
                <th scope="col"><?php echo htmlspecialchars($L['th_funding_type']); ?></th>
                <th scope="col"><?php echo htmlspecialchars($L['th_region']); ?></th>
                <th scope="col"><?php echo htmlspecialchars($L['th_fields']); ?></th>
                <th scope="col"><?php echo htmlspecialchars($L['th_link']); ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($rows as $r): $deadlineRaw = trim($r['deadline'] ?? ''); $deadlineSort = $deadlineRaw !== '' ? $deadlineRaw : '9999-12-31'; ?>
              <tr>
                <td><?php echo htmlspecialchars($r['source_name'] ?? ''); ?></td>
                <td class="font-medium"><?php echo htmlspecialchars($r['title'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($r['country'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($r['level'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($r['language'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($r['score'] ?? ''); ?></td>
                <td data-order="<?php echo htmlspecialchars($deadlineSort); ?>"><?php echo htmlspecialchars($r['deadline'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($r['amount'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($r['category'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($r['provider'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($r['funding_type'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($r['region'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($r['fields'] ?? ''); ?></td>
                <td><a class="btn btn-outline" rel="noopener noreferrer" href="<?php echo htmlspecialchars($r['item_url'] ?? '#'); ?>" target="_blank"><i class="fas fa-arrow-up-right-from-square"></i></a></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          <div class="mt-3 d-flex justify-between align-center" style="display:flex;gap:.5rem;align-items:center;justify-content:space-between;">
            <div id="info" class="text-xs" style="color:var(--text-secondary)" aria-live="polite"></div>
            <nav id="pager" class="pager d-flex align-center" style="display:flex;gap:.25rem;align-items:center;" aria-label="Pagination">
              <button class="btn btn-outline" id="prevPage" aria-label="<?php echo htmlspecialchars($L['pager_prev']); ?>"><?php echo htmlspecialchars($L['pager_prev']); ?></button>
              <div id="pagerNumbers" class="d-flex align-center" style="display:flex;gap:.25rem;"></div>
              <button class="btn btn-outline" id="nextPage" aria-label="<?php echo htmlspecialchars($L['pager_next']); ?>"><?php echo htmlspecialchars($L['pager_next']); ?></button>
            </nav>
          </div>
        </div>
      </section>

      <section x-show="tab==='orientation'" x-cloak role="tabpanel" id="panel-orientation" aria-labelledby="tab-orient" x-transition.opacity.scale>
        <div class="card">
          <h2 class="text-xl font-semibold mb-2"><?php echo htmlspecialchars($L['orientation_h2']); ?></h2>
          <p class="text-slate-300 mb-2"><?php echo htmlspecialchars($L['orientation_p1']); ?></p>
          <ol class="text-slate-300" style="padding-left:1.25rem; list-style: decimal;">
            <?php foreach (($L['orientation_steps'] ?? []) as $step): ?>
              <li class="mb-1"><?php echo htmlspecialchars($step); ?></li>
            <?php endforeach; ?>
          </ol>
          <div class="mt-3 d-flex" style="display:flex; gap:1rem; flex-wrap:wrap;">
            <a class="btn btn-outline" href="#filters"><i class="fas fa-filter"></i> <?php echo htmlspecialchars($L['jump_to_filters']); ?></a>
            <a class="btn btn-primary" href="mailto:contact@richard-hofrance.com"><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($L['orientation_cta']); ?></a>
          </div>
          <div class="mt-4">
            <div class="font-semibold mb-1"><?php echo htmlspecialchars($L['orientation_links']); ?></div>
            <ul class="text-slate-300" style="padding-left:1.25rem; list-style: disc;">
              <li><a class="text-cyan-300 underline" href="https://europa.eu/youth" target="_blank" rel="noopener noreferrer">EU Youth</a></li>
              <li><a class="text-cyan-300 underline" href="https://scholarshipportal.com" target="_blank" rel="noopener noreferrer">ScholarshipPortal</a></li>
              <li><a class="text-cyan-300 underline" href="https://www.unesco.org/en/education" target="_blank" rel="noopener noreferrer">UNESCO Education</a></li>
            </ul>
          </div>
        </div>
      </section>

      <section x-show="tab==='about'" x-cloak role="tabpanel" id="panel-about" aria-labelledby="tab-about" x-transition.opacity.scale>
        <div class="card">
          <div class="about-hero" style="padding:1rem;border-radius:12px;background:linear-gradient(135deg, rgba(34,211,238,.15), rgba(99,102,241,.12)); border:1px dashed var(--glass-border); margin-bottom:1rem;">
            <h2 class="text-2xl font-semibold mb-2"><?php echo $L['about_h2']; ?></h2>
            <p class="mb-2 text-slate-300"><?php echo $L['about_p1']; ?></p>
            <p class="mb-2 text-slate-300"><?php echo $L['about_p2']; ?></p>
            <p class="mb-2 text-slate-300"><?php echo $L['about_p3']; ?></p>
          </div>
          <div class="glass" role="note" aria-label="<?php echo htmlspecialchars($L['mission_title']); ?>" style="padding:1rem; margin-bottom:1rem;">
            <div class="font-semibold mb-1"><?php echo htmlspecialchars($L['mission_title']); ?></div>
            <p class="text-slate-300 mb-1"><?php echo $L['mission_text']; ?></p>
            <p class="text-slate-400 text-sm"><?php echo htmlspecialchars($L['mission_disclaimer']); ?></p>
            <div class="mt-2" style="display:flex; gap:.5rem; flex-wrap:wrap;">
              <a class="btn btn-primary" href="mailto:contact@richard-hofrance.com" aria-label="<?php echo htmlspecialchars($L['cta_contact']); ?>"><?php echo htmlspecialchars($L['cta_contribute']); ?></a>
              <a class="btn btn-outline" href="https://github.com/hofrance" target="_blank" rel="noopener noreferrer" aria-label="<?php echo htmlspecialchars($L['cta_github']); ?>"><?php echo htmlspecialchars($L['cta_github']); ?></a>
            </div>
          </div>
          <div class="d-flex" style="display:flex; gap:1rem; flex-wrap:wrap;">
            <div class="glass" style="padding:1rem; flex:1 1 220px;">
              <i class="fas fa-shield-halved" style="color:var(--info-color);"></i>
              <div class="mt-2">
                <div class="font-semibold"><?php echo htmlspecialchars($L['about_list1']); ?></div>
                <div class="text-slate-400 text-sm">SOC/SIEM · Pentest · Hardening</div>
              </div>
            </div>
            <div class="glass" style="padding:1rem; flex:1 1 220px;">
              <i class="fas fa-headset" style="color:var(--success-color);"></i>
              <div class="mt-2">
                <div class="font-semibold"><?php echo htmlspecialchars($L['about_list2']); ?></div>
                <div class="text-slate-400 text-sm">CERT/CSIRT · 24/7 IR</div>
              </div>
            </div>
            <div class="glass" style="padding:1rem; flex:1 1 220px;">
              <i class="fas fa-scale-balanced" style="color:var(--warning-color);"></i>
              <div class="mt-2">
                <div class="font-semibold"><?php echo htmlspecialchars($L['about_list3']); ?></div>
                <div class="text-slate-400 text-sm">GDPR · Risk · Training</div>
              </div>
            </div>
          </div>
          <div class="mt-4 flex items-center gap-3">
            <span class="text-slate-400 text-sm"><?php echo htmlspecialchars($L['about_follow']); ?>:</span>
            <a class="p-2 rounded hover:bg-white/10" href="https://www.linkedin.com/in/richard-hofrance-bankouezi/" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn">
              <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="#60a5fa"><path d="M4.98 3.5C4.98 4.88 3.86 6 2.5 6S0 4.88 0 3.5 1.12 1 2.5 1s2.48 1.12 2.48 2.5zM.5 8h4V24h-4zM8.5 8h3.8v2.2h.1c.5-.9 1.7-2.2 3.6-2.2 3.9 0 4.6 2.6 4.6 6V24h-4v-7.1c0-1.7 0-3.9-2.4-3.9-2.4 0-2.8 1.8-2.8 3.8V24h-4z"/></svg>
            </a>
            <a class="p-2 rounded hover:bg-white/10" href="https://github.com/hofrance" target="_blank" rel="noopener noreferrer" aria-label="GitHub">
              <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="#94a3b8"><path d="M12 0C5.37 0 0 5.37 0 12c0 5.3 3.44 9.8 8.2 11.39.6.11.82-.26.82-.58 0-.28-.01-1.03-.02-2.02-3.34.73-4.04-1.61-4.04-1.61-.55-1.4-1.34-1.78-1.34-1.78-1.09-.75.08-.73.08-.73 1.2.08 1.84 1.23 1.84 1.23 1.07 1.84 2.8 1.31 3.48 1 .11-.77.42-1.31.76-1.61-2.67-.3-5.47-1.33-5.47-5.93 0-1.31.47-2.38 1.23-3.22-.12-.3-.53-1.52.12-3.17 0 0 1.01-.32 3.3 1.23.96-.27 1.99-.41 3.01-.41s2.05.14 3.01.41c2.29-1.55 3.3-1.23 3.3-1.23.65 1.65.24 2.87.12 3.17.77.84 1.23 1.91 1.23 3.22 0 4.61-2.81 5.63-5.49 5.93.43.37.81 1.1.81 2.22 0 1.6-.02 2.88-.02 3.27 0 .32.22.7.83.58C20.56 21.8 24 17.3 24 12c0-6.63-5.37-12-12-12z"/></svg>
            </a>
            <a class="p-2 rounded hover:bg-white/10" href="mailto:contact@richard-hofrance.com" aria-label="Email">
              <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="#22d3ee"><path d="M12 13.065L.015 4.5C.24 3.635 1.03 3 2 3h20c.97 0 1.76.635 1.985 1.5L12 13.065zM12 15L24 6v12c0 1.105-.895 2-2 2H2c-1.105 0-2-.895-2-2V6l12 9z"/></svg>
            </a>
            <a class="p-2 rounded hover:bg-white/10" href="https://richard-hofrance.com/en" target="_blank" rel="noopener noreferrer" aria-label="Website">
              <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="#34d399"><path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm-1 17.93C7.062 19.435 4.565 16.938 4.07 13H7c.256 2.091 1.099 3.983 2 5.356v1.574zM4.07 11C4.565 7.062 7.062 4.565 11 4.07V5.644C10.099 7.017 9.256 8.909 9 11H4.07zM13 4.07C16.938 4.565 19.435 7.062 19.93 11H17c-.256-2.091-1.099-3.983-2-5.356V4.07zM17 13h2.93c-.495 3.938-2.992 6.435-6.93 6.93V18.356c.901-1.373 1.744-3.265 2-5.356zM12 6c1.657 0 3 2.239 3 5s-1.343 5-3 5-3-2.239-3-5 1.343-5 3-5z"/></svg>
            </a>
          </div>
        </div>
      </section>
    </main>
  </div>

  <!-- Mobile bottom action bar -->
  <nav class="mobile-actions" aria-label="Actions">
    <a href="#filters" class="m-btn" aria-label="<?php echo htmlspecialchars($L['jump_to_filters']); ?>"><i class="fas fa-filter"></i><span><?php echo htmlspecialchars($L['jump_to_filters']); ?></span></a>
    <button id="m-open-search" class="m-btn" aria-label="<?php echo htmlspecialchars($L['search']); ?>"><i class="fas fa-magnifying-glass"></i><span><?php echo htmlspecialchars($L['search']); ?></span></button>
    <button id="m-top" class="m-btn" aria-label="<?php echo htmlspecialchars($L['back_to_top']); ?>"><i class="fas fa-arrow-up"></i><span><?php echo htmlspecialchars($L['back_to_top']); ?></span></button>
  </nav>

  <button id="toTop" class="fab" aria-label="<?php echo htmlspecialchars($L['back_to_top']); ?>" title="<?php echo htmlspecialchars($L['back_to_top']); ?>"><i class="fas fa-arrow-up"></i></button>
  <script src="futuristic-ui.js"></script>
  <script nonce="<?php echo htmlspecialchars($nonce); ?>">
    // Enhance selects BEFORE DataTable init
    const chCountry = new Choices('#filter-country', { removeItemButton: true, shouldSort: true });
    const chLevel = new Choices('#filter-level', { removeItemButton: true, shouldSort: true });
    const chLang = new Choices('#filter-language', { removeItemButton: true, shouldSort: true });
    const chCategory = new Choices('#filter-category', { removeItemButton: true, shouldSort: true });
    const chProvider = new Choices('#filter-provider', { removeItemButton: true, shouldSort: true });
    const chFunding = new Choices('#filter-funding-type', { removeItemButton: true, shouldSort: true });
    const chRegion = new Choices('#filter-region', { removeItemButton: true, shouldSort: true });
    const chFields = new Choices('#filter-fields', { removeItemButton: true, shouldSort: true });

    // Date pickers
    const fpStart = flatpickr('#date-start', { dateFormat: 'Y-m-d' });
    const fpEnd = flatpickr('#date-end', { dateFormat: 'Y-m-d' });

    // Use server-side mode
    const serverSide = true;
    let dt = new DataTable('#tbl', {
      serverSide: serverSide,
      processing: serverSide,
      ajax: serverSide ? {
        url: 'api.php',
        type: 'POST',
        data: function (d) {
          d.countries = chCountry.getValue(true);
          d.levels = chLevel.getValue(true);
          d.langs = chLang.getValue(true);
          d.categories = chCategory.getValue(true);
          d.providers = chProvider.getValue(true);
          d.funding_types = chFunding.getValue(true);
          d.regions = chRegion.getValue(true);
          d.fields = chFields.getValue(true);
          d.date_start = document.querySelector('#date-start').value || '';
          d.date_end = document.querySelector('#date-end').value || '';
        }
      } : undefined,
      pageLength: 25,
      paging: true,
      lengthChange: false,
      info: true,
      dom: 't', // keep only table, custom controls elsewhere
      order: [[6, 'asc'], [5, 'desc']],
      columns: serverSide ? [
        { data: 'source_name' },
        { data: 'title' },
        { data: 'country' },
        { data: 'level' },
        { data: 'language' },
        { data: 'score' },
        { data: 'deadline' },
        { data: 'amount' },
        { data: 'category' },
        { data: 'provider' },
        { data: 'funding_type' },
        { data: 'region' },
        { data: 'fields' },
        { data: 'item_url', render: function(data){ return `<a class="text-cyan-400 underline" rel="noopener noreferrer" href="${data}" target="_blank"><?php echo htmlspecialchars($L['open']); ?></a>`; } },
      ] : undefined,
    });

    // Sticky filters using IntersectionObserver
    (function(){
      const sentinel = document.getElementById('filters-sentinel');
      const panel = document.getElementById('filters');
      if (!sentinel || !panel) return;
      const io = new IntersectionObserver((entries)=>{
        entries.forEach(en=>{
          panel.classList.toggle('is-stuck', !en.isIntersecting);
        });
      }, { rootMargin: '-80px 0px 0px 0px', threshold: [0, 1] });
      io.observe(sentinel);
    })();

    // Mobile actions shortcuts
    (function(){
      const openSearch = document.getElementById('m-open-search');
      const toTop = document.getElementById('m-top');
      const input = document.getElementById('global-search');
      openSearch?.addEventListener('click', ()=>{ input?.focus(); input?.scrollIntoView({behavior:'smooth', block:'center'}); });
      toTop?.addEventListener('click', ()=>{ window.scrollTo({ top: 0, behavior: 'smooth' }); });
    })();

    // Persist and handle Advanced Filters toggle
    (function(){
      const advWrap = document.getElementById('advanced-filters');
      const btn = document.getElementById('toggle-advanced');
      if (!advWrap || !btn) return;
      const key = 'ui.advancedOpen';
      let open = (localStorage.getItem(key) === '1');
      function render(){ advWrap.classList.toggle('hidden', !open); btn.setAttribute('aria-expanded', open.toString()); }
      btn.addEventListener('click', ()=>{ open = !open; localStorage.setItem(key, open ? '1' : '0'); render(); });
      render();
    })();

    // Active filter chips
    (function(){
      const box = document.getElementById('chips');
      const clearAll = document.getElementById('clear-all');
      const searchInput = document.getElementById('global-search');
      const m = {
        country: chCountry,
        level: chLevel,
        language: chLang,
        category: chCategory,
        provider: chProvider,
        funding_type: chFunding,
        region: chRegion,
        fields: chFields,
      };
      const labels = {
        country: <?php echo json_encode($L['f_country']); ?>,
        level: <?php echo json_encode($L['f_level']); ?>,
        language: <?php echo json_encode($L['f_lang']); ?>,
        category: <?php echo json_encode($L['f_category']); ?>,
        provider: <?php echo json_encode($L['f_provider']); ?>,
        funding_type: <?php echo json_encode($L['f_funding_type']); ?>,
        region: <?php echo json_encode($L['f_region']); ?>,
        fields: <?php echo json_encode($L['f_fields']); ?>,
        date_start: <?php echo json_encode($L['f_deadline_start']); ?>,
        date_end: <?php echo json_encode($L['f_deadline_end']); ?>,
        search: <?php echo json_encode($L['search']); ?>,
      };
      function renderChips(){
        if (!box) return;
        box.innerHTML = '';
        function addChip(type, value, display){
          const chip = document.createElement('span');
          chip.className = 'chip';
          chip.innerHTML = `<strong>${labels[type] || type}:</strong> <span>${display || value}</span> <button class="chip-remove" type="button" aria-label="Remove">&times;</button>`;
          chip.dataset.type = type; chip.dataset.value = value;
          box.appendChild(chip);
        }
        // Choices selections
        for (const [type, inst] of Object.entries(m)){
          try{
            const vals = inst.getValue(); // [{value,label}]
            vals.forEach((v) => addChip(type, v.value, v.label || v.value));
          }catch(e){}
        }
        // Dates
        const ds = document.querySelector('#date-start')?.value?.trim();
        const de = document.querySelector('#date-end')?.value?.trim();
        if (ds) addChip('date_start', ds, ds);
        if (de) addChip('date_end', de, de);
        // Search
        const sv = searchInput?.value?.trim();
        if (sv) addChip('search', sv, sv);
      }
      function removeOne(type, value){
        if (m[type]){
          try { m[type].removeActiveItemsByValue(value); } catch(e){}
        } else if (type === 'date_start'){
          try { fpStart.clear(); } catch(e){}
        } else if (type === 'date_end'){
          try { fpEnd.clear(); } catch(e){}
        } else if (type === 'search'){
          if (searchInput){ searchInput.value=''; dt.search(''); }
        }
        applyFilters();
        renderChips();
      }
      box?.addEventListener('click', (e)=>{
        const btn = e.target.closest('.chip-remove');
        if (!btn) return;
        const chip = e.target.closest('.chip');
        if (!chip) return;
        removeOne(chip.dataset.type, chip.dataset.value);
      });
      clearAll?.addEventListener('click', ()=>{ resetFilters(); if (searchInput){ searchInput.value=''; dt.search(''); } renderChips(); });
      // Re-render on changes
      const filterIds = ['#filter-country','#filter-level','#filter-language','#filter-category','#filter-provider','#filter-funding-type','#filter-region','#filter-fields','#date-start','#date-end','#global-search'];
      filterIds.forEach(id=>{ const el = document.querySelector(id); if (el) el.addEventListener('change', renderChips); });
      // Initial
      renderChips();
    })();

    // A11y: global search with debounce
    function debounce(fn, wait){ let t; return (...args)=>{ clearTimeout(t); t=setTimeout(()=>fn(...args), wait); }; }
    const searchInput = document.getElementById('global-search');
    const clearBtn = document.getElementById('clear-search');
    if (searchInput){
      const doSearch = debounce((v)=>{ dt.search(v).draw(); }, 250);
      searchInput.addEventListener('input', (e)=> doSearch(e.target.value));
      clearBtn.addEventListener('click', ()=>{ searchInput.value=''; dt.search('').draw(); searchInput.focus(); });
    }

    // Custom pager with numbers
    const prevBtn = document.getElementById('prevPage');
    const nextBtn = document.getElementById('nextPage');
    const pagerNumbers = document.getElementById('pagerNumbers');
    const infoBox = document.getElementById('info');

    function renderNumbers(info){
      pagerNumbers.innerHTML = '';
      const total = info.pages || 1;
      const cur = info.page;
      const start = Math.max(0, cur - 2);
      const end = Math.min(total - 1, cur + 2);
      function addBtn(i){
        const b = document.createElement('button');
        b.className = 'btn btn-outline page-btn';
        b.textContent = (i+1).toString();
        if (i === cur){ b.setAttribute('aria-current','page'); b.classList.add('active'); }
        b.setAttribute('aria-label', 'Page ' + (i+1));
        b.dataset.page = i;
        pagerNumbers.appendChild(b);
      }
      if (start > 0){ addBtn(0); if (start > 1){ const span = document.createElement('span'); span.textContent = '…'; span.style.padding='0 .25rem'; pagerNumbers.appendChild(span);} }
      for (let i=start; i<=end; i++){ addBtn(i); }
      if (end < total - 1){ if (end < total - 2){ const span2 = document.createElement('span'); span2.textContent = '…'; span2.style.padding='0 .25rem'; pagerNumbers.appendChild(span2);} addBtn(total-1); }
    }

    function updatePager(){
      const info = dt.page.info();
      infoBox.textContent = `${info.recordsDisplay} résultats`;
      prevBtn.disabled = info.page <= 0;
      nextBtn.disabled = info.page >= info.pages - 1;
      renderNumbers(info);
    }

    document.getElementById('page-size').addEventListener('change', () => {
      const v = document.getElementById('page-size').value;
      localStorage.setItem('ui.pageSize', v);
      dt.page.len(parseInt(v, 10)).draw();
      updatePager();
    });
    // Restore page size
    (function(){
      const saved = localStorage.getItem('ui.pageSize');
      if (saved){
        const sel = document.getElementById('page-size');
        if (sel){ sel.value = saved; dt.page.len(parseInt(saved,10)).draw(); }
      }
    })();

    // Keyboard navigation for pages (left/right arrows)
    document.addEventListener('keydown', (e)=>{
      if (document.querySelector('[x-data]')?.__x?.$data?.tab !== 'list') return;
      if (e.key === 'ArrowLeft'){ if (!prevBtn.disabled){ prevBtn.click(); } }
      if (e.key === 'ArrowRight'){ if (!nextBtn.disabled){ nextBtn.click(); } }
    });

    function applyFilters() {
      if (serverSide) { dt.ajax.reload(updatePager, false); return; }
      // client-side fallback (country/level/lang/date only)
      DataTable.ext.search = [function(settings, data) {
        const country = (data[2] || '').trim();
        const level = (data[3] || '').trim();
        const lang = (data[4] || '').trim();
        const deadline = (data[6] || '').trim();
        const selCountries = chCountry.getValue(true);
        const selLevels = chLevel.getValue(true);
        const selLangs = chLang.getValue(true);
        const start = document.querySelector('#date-start').value || '';
        const end = document.querySelector('#date-end').value || '';
        if (selCountries.length && !selCountries.includes(country)) return false;
        if (selLevels.length && !selLevels.includes(level)) return false;
        if (selLangs.length) {
          const langs = lang.split('|').map(s => s.trim());
          if (!selLangs.some(l => langs.includes(l))) return false;
        }
        if (start || end) {
          if (!deadline) return false;
          if (start && deadline < start) return false;
          if (end && deadline > end) return false;
        }
        return true;
      }];
      dt.draw();
      updatePager();
    }

    function resetFilters() {
      for (const ch of [chCountry, chLevel, chLang, chCategory, chProvider, chFunding, chRegion, chFields]) {
        try { ch.removeActiveItems(); } catch(e){}
      }
      try { fpStart.clear(); } catch(e){}
      try { fpEnd.clear(); } catch(e){}
      applyFilters();
    }

    for (const id of ['#filter-country','#filter-level','#filter-language','#filter-category','#filter-provider','#filter-funding-type','#filter-region','#filter-fields']) {
      document.querySelector(id).addEventListener('change', applyFilters);
    }
    document.querySelector('#date-start').addEventListener('change', applyFilters);
    document.querySelector('#date-end').addEventListener('change', applyFilters);
    document.getElementById('btn-reset').addEventListener('click', resetFilters);

    // Theme toggle remains unchanged
    (function() {
      const key = 'ui.theme';
      function setTheme(t){ document.documentElement.classList.toggle('auto-theme', t==='light'); document.documentElement.setAttribute('data-theme', t); localStorage.setItem(key, t); }
      const saved = localStorage.getItem(key); if (saved) setTheme(saved);
      document.getElementById('btn-theme').addEventListener('click', () => { const cur = document.documentElement.getAttribute('data-theme') || 'dark'; setTheme(cur === 'dark' ? 'light' : 'dark'); });
    })();
  </script>
</body>
</html>
