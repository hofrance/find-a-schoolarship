@extends('layouts.app')

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <!-- Breadcrumb -->
    <nav style="margin-bottom: 2rem;">
        <div style="display: flex; gap: 8px; align-items: center; color: var(--muted); font-size: 14px;">
            <a href="{{ route('detections.index') }}" style="color: var(--muted);">{{ __('navigation.home') }}</a>
            <span>›</span>
            <a href="{{ route('articles.index') }}" style="color: var(--muted);">{{ __('navigation.orientation') }}</a>
            <span>›</span>
            <a href="{{ route('articles.category', $article->category) }}" style="color: var(--muted);">{{ ucfirst($article->category) }}</a>
            <span>›</span>
            <span style="color: var(--text);">{{ $article->title }}</span>
        </div>
    </nav>

    <!-- Article -->
    <article class="card" style="padding: 2rem;">
        <!-- Métadonnées -->
        <div style="margin-bottom: 1.5rem;">
            <span class="badge badge-{{ $article->category }}">{{ ucfirst($article->category) }}</span>
            @if($article->tags)
                @foreach($article->tags as $tag)
                    <span class="badge" style="margin-left: 4px;">#{{ $tag }}</span>
                @endforeach
            @endif
        </div>

        <!-- Titre -->
        <h1 style="font-size: 2.5rem; font-weight: 700; margin-bottom: 1rem; color: var(--text);">
            {{ $article->title }}
        </h1>

        <!-- Informations -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; color: var(--muted); font-size: 14px;">
            <div style="display: flex; gap: 1rem; align-items: center;">
                <span>📅 {{ $article->published_at->format('d/m/Y') }}</span>
                @if($article->author)
                    <span>👤 {{ $article->author->name }}</span>
                @endif
            </div>
            <span>👁️ {{ $article->views_count }} vues</span>
        </div>

        <!-- Résumé -->
        <div style="background: linear-gradient(135deg, rgba(0,212,255,0.1), rgba(0,255,136,0.05)); border-left: 4px solid var(--primary); padding: 1rem; margin-bottom: 2rem; border-radius: var(--radius-md);">
            <p style="margin: 0; font-size: 1.1rem; font-weight: 500; color: var(--text);">{{ $article->excerpt }}</p>
        </div>

        <!-- Contenu -->
        <div style="line-height: 1.8; font-size: 1.1rem; color: var(--text);">
            {!! nl2br(e($article->content)) !!}
        </div>

        <!-- Partage -->
        <hr style="margin: 3rem 0; border: 0; height: 1px; background: var(--stroke);">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h6 style="margin-bottom: 0.5rem; color: var(--text);">Partager cet article :</h6>
                <div style="display: flex; gap: 8px;">
                    <a href="https://twitter.com/intent/tweet?text={{ urlencode($article->title) }}&url={{ urlencode(request()->url()) }}" 
                       class="btn btn-primary" target="_blank" rel="noopener">
                        🐦 Twitter
                    </a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" 
                       class="btn btn-primary" target="_blank" rel="noopener">
                        📘 Facebook
                    </a>
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->url()) }}" 
                       class="btn btn-primary" target="_blank" rel="noopener">
                        💼 LinkedIn
                    </a>
                </div>
            </div>
            <a href="{{ route('articles.index') }}" class="btn">
                ← Retour aux articles
            </a>
        </div>
    </article>

    <!-- Articles similaires -->
    @if($relatedArticles->count() > 0)
        <div style="margin-top: 3rem;">
            <h3 style="margin-bottom: 1.5rem; color: var(--text);">Articles similaires</h3>
            <div class="grid grid-3">
                @foreach($relatedArticles as $related)
                    <div class="card article-card">
                        <span class="badge badge-{{ $related->category }}">{{ ucfirst($related->category) }}</span>
                        <h4 style="margin: 0.5rem 0; font-size: 1.1rem;">
                            <a href="{{ route('articles.show', $related->slug) }}" style="color: inherit;">
                                {{ Str::limit($related->title, 60) }}
                            </a>
                        </h4>
                        <p style="color: var(--muted); font-size: 0.9rem; margin-bottom: 1rem; flex-grow: 1;">
                            {{ Str::limit($related->excerpt, 100) }}
                        </p>
                        <a href="{{ route('articles.show', $related->slug) }}" class="btn btn-primary">
                            {{ __('navigation.read_more') }}
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
