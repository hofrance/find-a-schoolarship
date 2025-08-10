@extends('layouts.app')

@section('content')
<!-- Hero Section -->
<div class="hero">
    <h1>🎓 {{ __('navigation.scholarships') }}</h1>
    <p>Découvrez les meilleures opportunités de bourses d'études dans le monde entier. Filtrez par pays, niveau d'études, langue et bien plus encore.</p>
</div>

<!-- Statistiques -->
<div class="card stats-grid" style="margin-bottom: 2rem;">
    <div class="stat-card">
        <span class="stat-value">{{ $detections->total() }}</span>
        <div class="stat-label">{{ __('navigation.results') }} trouvé(s)</div>
    </div>
    <div class="stat-card">
        <span class="stat-value">{{ $detections->where('deadline', '>', now())->count() }}</span>
        <div class="stat-label">Encore ouvertes</div>
    </div>
    <div class="stat-card">
        <span class="stat-value">{{ $detections->whereNotNull('amount')->count() }}</span>
        <div class="stat-label">Avec montant</div>
    </div>
    <div class="stat-card">
        <span class="stat-value">{{ round($detections->avg('score')) }}%</span>
        <div class="stat-label">Score moyen</div>
    </div>
</div>

<!-- Filtres et recherche -->
<div class="card search-filters">
    <form method="get" action="{{ route('detections.index') }}" class="input-group" style="margin-bottom: 1rem;">
        <span class="input-icon">🔍</span>
        <input class="input search-input" type="search" name="search" value="{{ request('search') }}" 
               placeholder="{{ __('navigation.search') }}..." style="flex: 1;">
        <input class="input" type="number" name="min_score" value="{{ request('min_score') }}" 
               placeholder="Score min" min="0" max="100" style="width: 120px;">
        <button class="btn btn-primary" type="submit">{{ __('navigation.filter') }}</button>
    </form>
    
    <!-- Filtres rapides -->
    <div class="chips">
        <span class="chip {{ request('country') == 'France' ? 'is-active' : '' }}" 
              onclick="toggleFilter('country', 'France')">🇫🇷 France</span>
        <span class="chip {{ request('country') == 'Canada' ? 'is-active' : '' }}" 
              onclick="toggleFilter('country', 'Canada')">🇨🇦 Canada</span>
        <span class="chip {{ request('country') == 'USA' ? 'is-active' : '' }}" 
              onclick="toggleFilter('country', 'USA')">🇺🇸 USA</span>
        <span class="chip {{ request('level') == 'Master' ? 'is-active' : '' }}" 
              onclick="toggleFilter('level', 'Master')">🎓 Master</span>
        <span class="chip {{ request('level') == 'Doctorat' ? 'is-active' : '' }}" 
              onclick="toggleFilter('level', 'Doctorat')">📚 Doctorat</span>
        <span class="chip {{ request('language') == 'Français' ? 'is-active' : '' }}" 
              onclick="toggleFilter('language', 'Français')">🇫🇷 Français</span>
        <span class="chip {{ request('language') == 'English' ? 'is-active' : '' }}" 
              onclick="toggleFilter('language', 'English')">🇬🇧 English</span>
    </div>
</div>

<!-- Tableau des bourses -->
<div class="card">
    <table class="table" data-datatable>
        <thead>
            <tr>
                <th class="th-sortable" data-type="string">{{ __('navigation.title') }}</th>
                <th class="th-sortable" data-type="string">{{ __('navigation.country') }}</th>
                <th class="th-sortable" data-type="string">{{ __('navigation.level') }}</th>
                <th class="th-sortable" data-type="string">{{ __('navigation.language_req') }}</th>
                <th class="th-sortable" data-type="string">{{ __('navigation.amount') }}</th>
                <th class="th-sortable" data-type="string">{{ __('navigation.deadline') }}</th>
                <th class="th-sortable" data-type="number">{{ __('navigation.score') }}</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($detections as $d)
            <tr class="searchable-card">
                <td style="max-width: 400px;">
                    <div style="font-weight: 600; margin-bottom: 4px;">
                        <a href="{{ $d->item_url }}" target="_blank" rel="noopener" style="color: var(--text);">
                            {{ e($d->title) }}
                        </a>
                    </div>
                    <div style="color: var(--muted); font-size: var(--font-sm);">
                        {{ e($d->source_name) }}
                    </div>
                </td>
                <td>
                    <span class="badge">{{ e($d->country) }}</span>
                </td>
                <td>
                    <span class="badge badge-scholarship">{{ e($d->level) }}</span>
                </td>
                <td>{{ e($d->language) }}</td>
                <td>
                    @if($d->amount)
                        <span style="font-weight: 600; color: var(--accent);">{{ e($d->amount) }}</span>
                    @else
                        <span style="color: var(--muted);">Non spécifié</span>
                    @endif
                </td>
                <td>
                    @if($d->deadline)
                        @php
                            $deadline = \Illuminate\Support\Carbon::parse($d->deadline);
                            $isUrgent = $deadline->diffInDays(now()) <= 30;
                        @endphp
                        <span class="badge {{ $isUrgent ? 'badge-danger' : 'badge-primary' }}">
                            {{ $deadline->isoFormat('DD MMM YYYY') }}
                        </span>
                        @if($isUrgent)
                            <div style="font-size: var(--font-xs); color: var(--danger); margin-top: 2px;">
                                ⚠️ {{ $deadline->diffForHumans() }}
                            </div>
                        @endif
                    @else
                        <span style="color: var(--muted);">—</span>
                    @endif
                </td>
                <td>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <div style="flex: 1; height: 4px; background: rgba(255,255,255,0.1); border-radius: 2px; overflow: hidden;">
                            <div style="height: 100%; background: linear-gradient(90deg, var(--danger), var(--warning), var(--success)); width: {{ $d->score }}%; border-radius: 2px;"></div>
                        </div>
                        <span style="font-weight: 700; min-width: 35px;">{{ $d->score }}%</span>
                    </div>
                </td>
                <td>
                    <div style="display: flex; gap: 8px;">
                        <a href="{{ $d->item_url }}" target="_blank" rel="noopener" class="btn btn-primary">
                            🔗 {{ __('navigation.apply_now') }}
                        </a>
                        @if($d->deadline && \Illuminate\Support\Carbon::parse($d->deadline)->isFuture())
                            <button class="btn" onclick="addToFavorites('{{ $d->id }}')" title="Ajouter aux favoris">
                                ⭐
                            </button>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Pagination -->
@if($detections->hasPages())
<nav class="pagination">
    {{-- Previous Page Link --}}
    @if ($detections->onFirstPage())
        <span class="btn" style="opacity: 0.5; cursor: not-allowed;">‹ Précédent</span>
    @else
        <a class="btn" href="{{ $detections->previousPageUrl() }}" rel="prev">‹ Précédent</a>
    @endif

    {{-- Pagination Elements --}}
    @php
        $start = max(1, $detections->currentPage() - 2);
        $end = min($detections->lastPage(), $detections->currentPage() + 2);
    @endphp

    @if($start > 1)
        <a class="btn" href="{{ $detections->url(1) }}">1</a>
        @if($start > 2)
            <span class="btn" style="opacity: 0.5;">...</span>
        @endif
    @endif

    @for($i = $start; $i <= $end; $i++)
        @if ($i == $detections->currentPage())
            <span class="btn btn-primary">{{ $i }}</span>
        @else
            <a class="btn" href="{{ $detections->url($i) }}">{{ $i }}</a>
        @endif
    @endfor

    @if($end < $detections->lastPage())
        @if($end < $detections->lastPage() - 1)
            <span class="btn" style="opacity: 0.5;">...</span>
        @endif
        <a class="btn" href="{{ $detections->url($detections->lastPage()) }}">{{ $detections->lastPage() }}</a>
    @endif

    {{-- Next Page Link --}}
    @if ($detections->hasMorePages())
        <a class="btn" href="{{ $detections->nextPageUrl() }}" rel="next">Suivant ›</a>
    @else
        <span class="btn" style="opacity: 0.5; cursor: not-allowed;">Suivant ›</span>
    @endif
</nav>
@endif

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser le DataTable pour le tri
    if (window.FUI && window.FUI.initDataTable) {
        const table = document.querySelector('table[data-datatable]');
        if (table) {
            window.FUI.initDataTable(table);
        }
    }
});

// Fonction pour les filtres rapides
function toggleFilter(param, value) {
    const url = new URL(window.location);
    const currentValue = url.searchParams.get(param);
    
    if (currentValue === value) {
        url.searchParams.delete(param);
    } else {
        url.searchParams.set(param, value);
    }
    
    window.location.href = url.toString();
}

// Fonction pour ajouter aux favoris (placeholder)
function addToFavorites(id) {
    if (window.FUI && window.FUI.toast) {
        window.FUI.toast('Ajouté aux favoris ! (fonctionnalité à implémenter)', 'success');
    }
}

// Recherche en temps réel améliorée
let searchTimeout;
document.addEventListener('input', function(e) {
    if (e.target.classList.contains('search-input')) {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr.searchable-card');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                const isVisible = text.includes(searchTerm);
                row.style.display = isVisible ? '' : 'none';
            });
            
            // Compter les résultats visibles
            const visibleCount = Array.from(rows).filter(row => row.style.display !== 'none').length;
            
            // Optionnel: afficher le nombre de résultats filtrés
            const existingCounter = document.querySelector('.filter-results-count');
            if (existingCounter) existingCounter.remove();
            
            if (searchTerm && visibleCount !== rows.length) {
                const counter = document.createElement('div');
                counter.className = 'filter-results-count';
                counter.style.cssText = 'margin-top: 1rem; padding: 0.5rem 1rem; background: rgba(0,212,255,0.1); border: 1px solid rgba(0,212,255,0.3); border-radius: var(--radius-md); color: var(--primary); text-align: center;';
                counter.textContent = `${visibleCount} résultat(s) trouvé(s) pour "${searchTerm}"`;
                document.querySelector('.card:last-of-type').after(counter);
            }
        }, 300);
    }
});
</script>
@endpush
