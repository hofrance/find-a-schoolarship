@extends('layouts.app')

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    <!-- Breadcrumb -->
    <nav style="margin-bottom: 2rem;">
        <div style="display: flex; gap: 8px; align-items: center; color: var(--muted); font-size: 14px;">
            <a href="{{ route('detections.index') }}" style="color: var(--muted);">{{ __('navigation.home') }}</a>
            <span>›</span>
            <a href="{{ route('careers.index') }}" style="color: var(--muted);">{{ __('navigation.careers') }}</a>
            @if($career->sectors && count($career->sectors) > 0)
                <span>›</span>
                <a href="{{ route('careers.sector', $career->sectors[0]) }}" style="color: var(--muted);">{{ $career->sectors[0] }}</a>
            @endif
            <span>›</span>
            <span style="color: var(--text);">{{ $career->title }}</span>
        </div>
    </nav>

    <!-- En-tête du métier -->
    <div class="card" style="padding: 2rem; margin-bottom: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
            <div style="flex: 1;">
                @if($career->is_featured)
                    <span class="badge badge-career" style="margin-bottom: 0.5rem;">⭐ En vedette</span>
                @endif
                <h1 style="font-size: 2.5rem; font-weight: 700; margin: 0 0 1rem 0; color: var(--text);">
                    {{ $career->title }}
                </h1>
                @if($career->sectors)
                    <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                        @foreach($career->sectors as $sector)
                            <a href="{{ route('careers.sector', $sector) }}" class="badge" style="text-decoration: none;">{{ $sector }}</a>
                        @endforeach
                    </div>
                @endif
            </div>
            
            @if($career->salary_range)
                <div style="background: linear-gradient(135deg, rgba(0,255,136,0.2), rgba(0,255,136,0.1)); border: 1px solid rgba(0,255,136,0.3); border-radius: var(--radius-lg); padding: 1.5rem; text-align: center; min-width: 160px;">
                    <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">💰</div>
                    <div style="font-weight: 600; color: var(--success);">{{ $career->salary_range }}</div>
                    <div style="font-size: 0.8rem; color: var(--muted); margin-top: 0.25rem;">Salaire annuel</div>
                </div>
            @endif
        </div>

        <!-- Description -->
        <div style="background: linear-gradient(135deg, rgba(0,212,255,0.1), rgba(0,255,136,0.05)); border-left: 4px solid var(--primary); padding: 1.5rem; border-radius: var(--radius-md); margin-bottom: 1.5rem;">
            <h3 style="margin: 0 0 0.5rem 0; color: var(--text); font-size: 1.2rem;">📋 Description du métier</h3>
            <p style="margin: 0; font-size: 1.1rem; line-height: 1.6; color: var(--text);">{{ $career->description }}</p>
        </div>

        <!-- Métadonnées -->
        <div style="display: flex; justify-content: space-between; align-items: center; color: var(--muted); font-size: 14px;">
            <div style="display: flex; gap: 1rem; align-items: center;">
                <span>👁️ {{ $career->views_count }} vues</span>
                @if($career->education_levels)
                    <span>🎓 {{ implode(', ', $career->education_levels) }}</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Sections détaillées -->
    <div class="grid grid-2" style="margin-bottom: 2rem;">
        <!-- Compétences requises -->
        @if($career->skills)
            <div class="card" style="padding: 1.5rem;">
                <h3 style="margin: 0 0 1rem 0; color: var(--text); display: flex; align-items: center; gap: 8px;">
                    🛠️ Compétences requises
                </h3>
                <div style="line-height: 1.6; color: var(--text);">
                    {!! nl2br(e($career->skills)) !!}
                </div>
            </div>
        @endif

        <!-- Formation -->
        @if($career->requirements)
            <div class="card" style="padding: 1.5rem;">
                <h3 style="margin: 0 0 1rem 0; color: var(--text); display: flex; align-items: center; gap: 8px;">
                    🎓 Formation & Prérequis
                </h3>
                <div style="line-height: 1.6; color: var(--text);">
                    {!! nl2br(e($career->requirements)) !!}
                </div>
            </div>
        @endif
    </div>

    <!-- Perspectives de carrière -->
    @if($career->career_prospects)
        <div class="card" style="padding: 2rem; margin-bottom: 2rem;">
            <h3 style="margin: 0 0 1rem 0; color: var(--text); display: flex; align-items: center; gap: 8px;">
                🚀 Perspectives de carrière
            </h3>
            <div style="line-height: 1.7; color: var(--text); font-size: 1.05rem;">
                {!! nl2br(e($career->career_prospects)) !!}
            </div>
        </div>
    @endif

    <!-- Actions -->
    <div class="card" style="padding: 1.5rem; text-align: center;">
        <div style="display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap;">
            <a href="{{ route('careers.index') }}" class="btn">
                ← Retour aux métiers
            </a>
            <a href="{{ route('articles.index') }}" class="btn btn-primary">
                🧭 Conseils d'orientation
            </a>
            <a href="{{ route('detections.index') }}" class="btn btn-success">
                🎓 Rechercher des bourses
            </a>
        </div>
    </div>

    <!-- Métiers similaires -->
    @if($relatedCareers->count() > 0)
        <div style="margin-top: 3rem;">
            <h3 style="margin-bottom: 1.5rem; color: var(--text);">Métiers similaires</h3>
            <div class="grid grid-2">
                @foreach($relatedCareers as $related)
                    <div class="card career-card">
                        <div class="career-meta">
                            @if($related->is_featured)
                                <span class="badge badge-career">⭐ En vedette</span>
                            @endif
                            @if($related->sectors)
                                @foreach($related->sectors->take(2) as $sector)
                                    <span class="badge">{{ $sector }}</span>
                                @endforeach
                            @endif
                        </div>
                        
                        <h4 style="margin: 0.5rem 0; font-size: 1.2rem;">
                            <a href="{{ route('careers.show', $related->slug) }}" style="color: inherit;">
                                {{ $related->title }}
                            </a>
                        </h4>
                        
                        <p style="color: var(--muted); font-size: 0.95rem; margin-bottom: 1rem; flex-grow: 1;">
                            {{ Str::limit($related->description, 100) }}
                        </p>
                        
                        @if($related->salary_range)
                            <div style="color: var(--success); font-weight: 600; margin-bottom: 1rem;">
                                💰 {{ $related->salary_range }}
                            </div>
                        @endif
                        
                        <a href="{{ route('careers.show', $related->slug) }}" class="btn btn-primary">
                            Découvrir
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
