@extends('layouts.app')

@section('content')
<div class="hero">
    <h1>{{ __('navigation.orientation') }}</h1>
    <p>Découvrez nos conseils et guides pour vous aider dans votre orientation scolaire et professionnelle.</p>
</div>

<!-- Filtres et recherche -->
<div class="card search-filters">
    <form method="GET" action="{{ route('articles.index') }}">
        <div class="input-group">
            <span class="input-icon">🔍</span>
            <input type="text" name="search" class="input search-input" 
                   placeholder="{{ __('navigation.search') }}..." 
                   value="{{ request('search') }}">
            <button type="submit" class="btn btn-primary">
                {{ __('navigation.search') }}
            </button>
        </div>
        
        <div class="chips">
            <a href="{{ route('articles.index') }}" 
               class="chip {{ !request('category') ? 'is-active' : '' }}">
                Tous les articles
            </a>
            <a href="{{ route('articles.index', ['category' => 'orientation']) }}" 
               class="chip {{ request('category') == 'orientation' ? 'is-active' : '' }}">
                Orientation
            </a>
            <a href="{{ route('articles.index', ['category' => 'etudes']) }}" 
               class="chip {{ request('category') == 'etudes' ? 'is-active' : '' }}">
                Études
            </a>
            <a href="{{ route('articles.index', ['category' => 'conseils']) }}" 
               class="chip {{ request('category') == 'conseils' ? 'is-active' : '' }}">
                Conseils
            </a>
        </div>
    </form>
</div>

<!-- Statistiques -->
@php
    $totalViews = \App\Models\Article::published()->locale()->sum('views_count');
@endphp
<div class="stats-grid">
    <div class="card stat-card">
        <span class="stat-value">{{ $articles->total() }}</span>
        <div class="stat-label">Articles</div>
    </div>
    <div class="card stat-card">
        <span class="stat-value">{{ $categories->count() }}</span>
        <div class="stat-label">Catégories</div>
    </div>
    <div class="card stat-card">
        <span class="stat-value">{{ number_format($totalViews) }}</span>
        <div class="stat-label">Lectures</div>
    </div>
</div>

<!-- Articles -->
@if($articles->count() > 0)
    <div class="grid grid-3">
        @foreach($articles as $article)
            <article class="card article-card searchable-card">
                <div class="article-meta">
                    <span class="badge badge-{{ $article->category }}">
                        {{ ucfirst($article->category) }}
                    </span>
                    <small style="color: var(--muted);">
                        📅 {{ $article->published_at->format('d/m/Y') }}
                    </small>
                    <small style="color: var(--muted);">
                        👁️ {{ $article->views_count }} vues
                    </small>
                </div>
                
                <h3>
                    <a href="{{ route('articles.show', $article->slug) }}" style="color: inherit;">
                        {{ $article->title }}
                    </a>
                </h3>
                
                <p class="excerpt">{{ $article->excerpt }}</p>
                
                <div style="margin-top: auto;">
                    @if($article->tags)
                        <div style="display: flex; gap: 4px; flex-wrap: wrap; margin-bottom: var(--space-3);">
                            @foreach($article->tags as $tag)
                                <span class="badge">#{{ $tag }}</span>
                            @endforeach
                        </div>
                    @endif
                    
                    <a href="{{ route('articles.show', $article->slug) }}" class="btn btn-primary">
                        {{ __('navigation.read_more') }}
                    </a>
                </div>
            </article>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="pagination">
        {{ $articles->appends(request()->query())->links() }}
    </div>
@else
    <div class="card" style="padding: 3rem; text-align: center;">
        <h3>{{ __('navigation.no_results') }}</h3>
        <p style="color: var(--muted);">Aucun article ne correspond à vos critères de recherche.</p>
        <a href="{{ route('articles.index') }}" class="btn btn-primary">
            Voir tous les articles
        </a>
    </div>
@endif
@endsection
