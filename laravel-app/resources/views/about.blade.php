@extends('layouts.app')

@section('content')
<div class="hero">
  <h1>Cybersecurity Architect & Digital Sentinel</h1>
  <p>
    Je conçois des défenses robustes contre les menaces numériques, de l’audit proactif à la réponse d’urgence aux incidents. Basé à Paris et disponible 24/7, j’allie approche offensive et défensive pour sécuriser vos systèmes.
  </p>
  <div style="margin-top: 16px; display:flex; gap:12px; flex-wrap:wrap;">
    <a class="btn" href="https://richard-hofrance.com/en/contact" target="_blank" rel="noopener">Audit gratuit</a>
    <a class="btn" href="https://richard-hofrance.com/en/projects" target="_blank" rel="noopener">Voir mes projets</a>
  </div>
</div>

<div class="stats" style="margin-top: 24px;">
  <div class="stat">
    <div class="stat-value">2</div>
    <div class="stat-label">Années d’expérience</div>
  </div>
  <div class="stat">
    <div class="stat-value">121</div>
    <div class="stat-label">Vulnérabilités trouvées</div>
  </div>
  <div class="stat">
    <div class="stat-value">3</div>
    <div class="stat-label">Clients protégés</div>
  </div>
  <div class="stat">
    <div class="stat-value">2</div>
    <div class="stat-label">Certifications</div>
  </div>
</div>

<div class="grid grid-2" style="margin-top: 24px;">
  <div class="card" style="padding: 20px;">
    <h2>Qui suis‑je ?</h2>
    <p style="color: var(--muted);">
      Expert en cybersécurité spécialisé dans la protection des infrastructures critiques et la réponse aux incidents. Ma mission : prévenir, détecter et neutraliser les menaces tout en assurant conformité et résilience.
    </p>
    <p style="color: var(--muted);">
      Domaines : SOC & SIEM, Incident Response (CERT/CSIRT), Threat Intelligence (MITRE ATT&CK), Sécurité Cloud, Conformité & RGPD.
    </p>
  </div>
  <div class="card" style="padding: 20px;">
    <h2>Compétences clés</h2>
    <div style="display:flex; flex-wrap:wrap; gap:8px;">
      <span class="badge">SOC & SIEM Monitoring</span>
      <span class="badge">Incident Response</span>
      <span class="badge">Threat Intelligence</span>
      <span class="badge">Sécurité Cloud</span>
      <span class="badge">Conformité & RGPD</span>
      <span class="badge">Pentest & Audits</span>
    </div>
  </div>
</div>

<div class="grid grid-2" style="margin-top: 24px;">
  <div class="card" style="padding: 20px;">
    <h2>Services</h2>
    <ul style="color: var(--muted); line-height:1.8;">
      <li>Audits de sécurité & tests d’intrusion</li>
      <li>Réponse à incident & accompagnement CERT/CSIRT</li>
      <li>Threat Intelligence & veille (MITRE ATT&CK)</li>
      <li>Sécurité Cloud (AWS/Azure/GCP)</li>
      <li>Conformité & RGPD, gestion des risques</li>
      <li>Formations et sensibilisation</li>
    </ul>
    <div style="margin-top:12px; display:flex; gap:10px; flex-wrap:wrap;">
      <a class="btn" href="https://richard-hofrance.com/en/services" target="_blank" rel="noopener">Voir les services</a>
      <a class="btn" href="https://richard-hofrance.com/en/trainings" target="_blank" rel="noopener">Formations</a>
    </div>
  </div>
  <div class="card" style="padding: 20px;">
    <h2>Certifications & Contact</h2>
    <div style="display:flex; flex-wrap:wrap; gap:8px; margin-bottom:12px;">
      <span class="badge">CISSP</span>
      <span class="badge">CISM</span>
      <span class="badge">CEH</span>
    </div>
    <p style="color: var(--muted);">
      Email : <a href="mailto:contact@richard-hofrance.com">contact@richard-hofrance.com</a><br>
      Localisation : Paris, France — Disponible 24/7 pour les urgences
    </p>
    <div style="display:flex; gap:10px; flex-wrap:wrap;">
      <a class="btn" href="https://www.linkedin.com/in/richard-hofrance-bankouezi/" target="_blank" rel="noopener">LinkedIn</a>
      <a class="btn" href="http://github.com/hofrance/" target="_blank" rel="noopener">GitHub</a>
      <a class="btn" href="https://richard-hofrance.com/en" target="_blank" rel="noopener">Site principal</a>
    </div>
  </div>
</div>

<div class="card" style="padding: 20px; margin-top:24px;">
  <h2>Éloges & engagement</h2>
  <p style="color: var(--muted);">
    Reconnu pour une approche à 360°, je combine rigueur opérationnelle, pédagogie et réactivité. Mon objectif : instaurer une sécurité efficace, compréhensible et durable — avec un accompagnement humain et des résultats mesurables.
  </p>
</div>

<div class="grid grid-2" style="margin-top: 24px;">
  <div class="card" style="padding: 20px;">
    <h2>Études de cas</h2>
    <ul style="color: var(--muted); line-height:1.8;">
      <li>Déploiement SIEM et runbook d’escalade pour PME — réduction du MTTR et meilleure visibilité des menaces.</li>
      <li>Réponse à incident ransomware — confinement, restauration et durcissement post‑mortem.</li>
      <li>Durcissement Cloud (AWS/Azure) — IAM, surveillance, conformité et alerting.</li>
    </ul>
  </div>
  <div class="card" style="padding: 20px;">
    <h2>Témoignages</h2>
    <blockquote style="color: var(--muted); margin:0;">
      « Ajoutez ici vos citations clients (secteur, résultat, bénéfice). »
    </blockquote>
    <p style="color: var(--muted); margin-top:8px; font-size:0.95em;">Astuce : 2‑3 phrases, un chiffre clé, et le contexte.</p>
  </div>
</div>

<div class="card" style="padding: 20px; margin-top:24px;">
  <h2>Portrait</h2>
  <div style="display:flex; gap:16px; align-items:center; flex-wrap:wrap;">
    @if(config('app.portrait_url'))
      <img src="{{ config('app.portrait_url') }}" alt="Portrait" style="width:120px;height:120px;border-radius:50%;object-fit:cover;background:var(--glass);" />
    @else
      <div style="width:120px;height:120px;border-radius:50%;background:var(--glass);display:grid;place-items:center;color:var(--muted);">Photo</div>
    @endif
    <p style="color: var(--muted);">
      Définissez une photo via config('app.portrait_url') ou nous pouvons intégrer votre image hébergée (par exemple sur votre site principal).
    </p>
  </div>
</div>
@endsection
