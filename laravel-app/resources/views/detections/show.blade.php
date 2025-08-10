@extends('layouts.app')

@section('content')
<div style="margin-bottom: 2rem;">
    <a href="{{ route('detections.index') }}" class="btn" style="margin-bottom: 1rem;">
        ← {{ __('navigation.back') }}
    </a>
</div>

<div class="grid grid-3" style="gap: 2rem; align-items: start;">
    <!-- Contenu principal -->
    <div style="grid-column: span 2;">
        <div class="card" style="margin-bottom: 2rem;">
            <div style="padding: 2rem;">
                <!-- En-tête avec titre et badges -->
                <div style="margin-bottom: 2rem;">
                    <h1 style="margin: 0 0 1rem 0; font-size: 2rem; font-weight: 700; color: var(--text); line-height: 1.2;">
                        {{ e($detection->title) }}
                    </h1>
                    
                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 1rem;">
                        <span class="badge badge-scholarship">{{ e($detection->level) }}</span>
                        <span class="badge">{{ e($detection->country) }}</span>
                        <span class="badge badge-orientation">{{ e($detection->language) }}</span>
                        @if($detection->deadline && \Illuminate\Support\Carbon::parse($detection->deadline)->isFuture())
                            <span class="badge badge-gov">🕒 Ouvert</span>
                        @elseif($detection->deadline)
                            <span class="badge" style="color: var(--danger); border-color: rgba(255,107,107,0.35);">❌ Fermé</span>
                        @endif
                    </div>
                    
                    <div style="color: var(--muted); margin-bottom: 1.5rem;">
                        <strong>Source:</strong> {{ e($detection->source_name) }}
                    </div>
                </div>

                <!-- Résumé/Description -->
                @if($detection->summary)
                <div style="margin-bottom: 2rem;">
                    <h3 style="color: var(--text); margin-bottom: 1rem;">📄 Description</h3>
                    <div style="color: var(--muted); line-height: 1.6; padding: 1.5rem; background: rgba(255,255,255,0.03); border: 1px solid var(--stroke); border-radius: var(--radius-md);">
                        {{ e($detection->summary) }}
                    </div>
                </div>
                @endif

                <!-- Bouton d'action principal -->
                <div style="text-align: center; margin-top: 2rem;">
                    <a href="{{ $detection->item_url }}" target="_blank" rel="noopener" class="btn btn-primary" style="font-size: 1.1rem; padding: 1rem 2rem;">
                        🚀 {{ __('navigation.apply_now') }}
                    </a>
                    <div style="color: var(--muted); font-size: var(--font-sm); margin-top: 0.5rem;">
                        Vous serez redirigé vers le site officiel
                    </div>
                </div>
            </div>
        </div>

        <!-- Conseils pour postuler -->
        <div class="card">
            <div style="padding: 1.5rem;">
                <h3 style="color: var(--text); margin-bottom: 1rem;">💡 Conseils pour postuler</h3>
                <div style="display: grid; gap: 1rem;">
                    <div style="display: flex; gap: 1rem; align-items: start;">
                        <span style="color: var(--primary); font-size: 1.2rem;">📝</span>
                        <div>
                            <strong style="color: var(--text);">Préparez vos documents</strong>
                            <div style="color: var(--muted); font-size: var(--font-sm);">CV, lettres de motivation, relevés de notes, certificats</div>
                        </div>
                    </div>
                    <div style="display: flex; gap: 1rem; align-items: start;">
                        <span style="color: var(--accent); font-size: 1.2rem;">⏰</span>
                        <div>
                            <strong style="color: var(--text);">Respectez les délais</strong>
                            <div style="color: var(--muted); font-size: var(--font-sm);">Préparez votre dossier bien à l'avance</div>
                        </div>
                    </div>
                    <div style="display: flex; gap: 1rem; align-items: start;">
                        <span style="color: var(--secondary); font-size: 1.2rem;">🎯</span>
                        <div>
                            <strong style="color: var(--text);">Personnalisez votre candidature</strong>
                            <div style="color: var(--muted); font-size: var(--font-sm);">Adaptez votre motivation aux spécificités du programme</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar avec informations -->
    <div>
        <!-- Informations clés -->
        <div class="card" style="margin-bottom: 1.5rem;">
            <div style="padding: 1.5rem;">
                <h3 style="color: var(--text); margin-bottom: 1.5rem; text-align: center;">ℹ️ Informations</h3>
                
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <!-- Score -->
                    <div>
                        <div style="color: var(--muted); font-size: var(--font-sm); margin-bottom: 0.5rem;">Score de correspondance</div>
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <div style="flex: 1; height: 8px; background: rgba(255,255,255,0.1); border-radius: 4px; overflow: hidden;">
                                <div style="height: 100%; background: linear-gradient(90deg, var(--danger), var(--warning), var(--success)); width: {{ $detection->score }}%; border-radius: 4px;"></div>
                            </div>
                            <span style="font-weight: 700; font-size: 1.2rem; color: var(--primary);">{{ $detection->score }}%</span>
                        </div>
                    </div>

                    <!-- Montant -->
                    @if($detection->amount)
                    <div>
                        <div style="color: var(--muted); font-size: var(--font-sm);">💰 Montant</div>
                        <div style="font-weight: 600; color: var(--accent); font-size: 1.1rem;">{{ e($detection->amount) }}</div>
                    </div>
                    @endif

                    <!-- Échéance -->
                    @if($detection->deadline)
                    <div>
                        <div style="color: var(--muted); font-size: var(--font-sm);">⏳ Échéance</div>
                        @php
                            $deadline = \Illuminate\Support\Carbon::parse($detection->deadline);
                            $isUrgent = $deadline->diffInDays(now()) <= 30;
                            $isFuture = $deadline->isFuture();
                        @endphp
                        <div style="font-weight: 600; color: {{ $isFuture ? ($isUrgent ? 'var(--warning)' : 'var(--success)') : 'var(--danger)' }};">
                            {{ $deadline->isoFormat('DD MMMM YYYY') }}
                        </div>
                        <div style="color: var(--muted); font-size: var(--font-sm);">
                            {{ $deadline->diffForHumans() }}
                        </div>
                    </div>
                    @endif

                    <!-- Pays -->
                    <div>
                        <div style="color: var(--muted); font-size: var(--font-sm);">🌍 Destination</div>
                        <div style="font-weight: 600; color: var(--text);">{{ e($detection->country) }}</div>
                    </div>

                    <!-- Niveau -->
                    <div>
                        <div style="color: var(--muted); font-size: var(--font-sm);">🎓 Niveau requis</div>
                        <div style="font-weight: 600; color: var(--text);">{{ e($detection->level) }}</div>
                    </div>

                    <!-- Langue -->
                    <div>
                        <div style="color: var(--muted); font-size: var(--font-sm);">🗣️ Langue</div>
                        <div style="font-weight: 600; color: var(--text);">{{ e($detection->language) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions rapides -->
        <div class="card">
            <div style="padding: 1.5rem;">
                <h4 style="color: var(--text); margin-bottom: 1rem; text-align: center;">⚡ Actions</h4>
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <button class="btn" onclick="shareScholarship()" style="width: 100%;">
                        📤 Partager
                    </button>
                    <button class="btn" onclick="addToFavorites('{{ $detection->id }}')" style="width: 100%;">
                        ⭐ Favoris
                    </button>
                    <button class="btn" onclick="window.FUI?.copyToClipboard('{{ $detection->item_url }}')" style="width: 100%;">
                        📋 Copier le lien
                    </button>
                    <a href="{{ route('detections.index') }}?country={{ urlencode($detection->country) }}" class="btn" style="width: 100%; text-align: center;">
                        🔍 Bourses similaires
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function shareScholarship() {
    if (navigator.share) {
        navigator.share({
            title: '{{ e($detection->title) }}',
            text: 'Découvrez cette opportunité de bourse : {{ e($detection->title) }}',
            url: window.location.href
        });
    } else {
        // Fallback pour les navigateurs sans support
        window.FUI?.copyToClipboard(window.location.href);
        window.FUI?.toast('Lien copié dans le presse-papier !', 'success');
    }
}

function addToFavorites(id) {
    // Ici vous pouvez implémenter la logique pour sauvegarder en favoris
    // Pour l'instant, on simule avec localStorage
    let favorites = JSON.parse(localStorage.getItem('scholarship_favorites') || '[]');
    
    if (!favorites.includes(id)) {
        favorites.push(id);
        localStorage.setItem('scholarship_favorites', JSON.stringify(favorites));
        window.FUI?.toast('Ajouté aux favoris !', 'success');
    } else {
        window.FUI?.toast('Déjà dans vos favoris', 'info');
    }
}

// Animation d'entrée
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        
        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 150);
    });
});
</script>
@endpush
