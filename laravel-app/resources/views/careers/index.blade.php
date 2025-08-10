@extends('layouts.app')

@section('content')
<div class="hero">
    <h1>{{ __('navigation.careers') }}</h1>
    <p>Explorez les métiers d'avenir et découvrez les formations qui y mènent.</p>
</div>

<!-- Filtres et recherche -->
<div class="card search-filters">
    <form method="GET" action="{{ route('careers.index') }}">
        <div class="input-group">
            <span class="input-icon">🔍</span>
            <input type="text" name="search" class="input search-input" 
                   placeholder="Rechercher un métier..." 
                   value="{{ request('search') }}">
            <button type="submit" class="btn btn-primary">
                {{ __('navigation.search') }}
            </button>
        </div>
        
        <div class="chips">
            <a href="{{ route('careers.index') }}" 
               class="chip {{ !request('sector') ? 'is-active' : '' }}">
                Tous les secteurs
            </a>
            @foreach($sectors->take(6) as $sector)
                <a href="{{ route('careers.index', ['sector' => $sector]) }}" 
                   class="chip {{ request('sector') == $sector ? 'is-active' : '' }}">
                    {{ ucfirst($sector) }}
                </a>
            @endforeach
        </div>
    </form>
</div>

<!-- Statistiques -->
<div class="stats-grid">
    <div class="card stat-card">
        <span class="stat-value">{{ $careers->total() }}</span>
        <div class="stat-label">Métiers</div>
    </div>
    <div class="card stat-card">
        <span class="stat-value">{{ $sectors->count() }}</span>
        <div class="stat-label">Secteurs</div>
    </div>
    <div class="card stat-card">
        <span class="stat-value">{{ $featuredCareers->count() }}</span>
        <div class="stat-label">En vedette</div>
    </div>
    <div class="card stat-card">
        <span class="stat-value">{{ number_format($careers->sum('views_count')) }}</span>
        <div class="stat-label">Consultations</div>
    </div>
</div>

<!-- Métiers en vedette -->
@if($featuredCareers->count() > 0)
    <div style="margin-bottom: 3rem;">
        <h2 style="margin-bottom: 1.5rem; color: var(--text);">🌟 Métiers en vedette</h2>
        <div class="grid grid-3">
            @foreach($featuredCareers as $career)
                <div class="card career-card searchable-card animate-glow">
                    <div class="career-meta">
                        <span class="badge badge-career">⭐ En vedette</span>
                        @if($career->sectors)
                            @foreach($career->sectors as $sector)
                                <span class="badge">{{ $sector }}</span>
                            @endforeach
                        @endif
                    </div>
                    
                    <h3>
                        <a href="{{ route('careers.show', $career->slug) }}" style="color: inherit;">
                            {{ $career->title }}
                        </a>
                    </h3>
                    
                    <p class="description">{{ Str::limit($career->description, 120) }}</p>
                    
                    <div style="margin-top: auto;">
                        @if($career->salary_range)
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: var(--space-3); color: var(--success);">
                                <span>💰</span>
                                <strong>{{ $career->salary_range }}</strong>
                            </div>
                        @endif
                        
                        <a href="{{ route('careers.show', $career->slug) }}" class="btn btn-primary">
                            Découvrir ce métier
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

<!-- Tous les métiers -->
<h2 style="margin-bottom: 1.5rem; color: var(--text);">💼 Tous les métiers</h2>

@if($careers->count() > 0)
    <div class="grid grid-2">
        @foreach($careers as $career)
            <div class="card career-card searchable-card">
                <div class="career-meta">
                    @if($career->is_featured)
                        <span class="badge badge-career">⭐ En vedette</span>
                    @endif
                    @if($career->sectors)
                        @foreach(array_slice($career->sectors, 0, 2) as $sector)
                            <span class="badge">{{ $sector }}</span>
                        @endforeach
                    @endif
                    <small style="color: var(--muted);">👁️ {{ $career->views_count }} vues</small>
                </div>
                
                <h3>
                    <a href="{{ route('careers.show', $career->slug) }}" style="color: inherit;">
                        {{ $career->title }}
                    </a>
                </h3>
                
                <p class="description">{{ Str::limit($career->description, 150) }}</p>
                
                <div style="margin-top: auto;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--space-3);">
                        @if($career->salary_range)
                            <div style="color: var(--success); font-weight: 600;">
                                💰 {{ $career->salary_range }}
                            </div>
                        @endif
                        
                        @if($career->education_levels)
                            <div style="color: var(--muted); font-size: 0.9rem;">
                                🎓 {{ implode(', ', $career->education_levels) }}
                            </div>
                        @endif
                    </div>
                    
                    <a href="{{ route('careers.show', $career->slug) }}" class="btn btn-primary">
                        En savoir plus
                    </a>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="pagination">
        {{ $careers->appends(request()->query())->links() }}
    </div>
@else
    <div class="card" style="padding: 3rem; text-align: center;">
        <h3>{{ __('navigation.no_results') }}</h3>
        <p style="color: var(--muted);">Aucun métier ne correspond à vos critères de recherche.</p>
        <a href="{{ route('careers.index') }}" class="btn btn-primary">
            Voir tous les métiers
        </a>
    </div>
@endif
@endsection
