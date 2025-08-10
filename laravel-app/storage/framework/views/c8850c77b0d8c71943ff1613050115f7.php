<?php $__env->startSection('content'); ?>
<!-- Hero Section -->
<div class="hero">
    <h1>🎓 <?php echo e(__('navigation.scholarships')); ?></h1>
    <p>Découvrez les meilleures opportunités de bourses d'études dans le monde entier. Filtrez par pays, niveau d'études, langue et bien plus encore.</p>
</div>

<!-- Statistiques -->
<div class="card stats-grid" style="margin-bottom: 2rem;">
    <div class="stat-card">
        <span class="stat-value"><?php echo e($detections->total()); ?></span>
        <div class="stat-label"><?php echo e(__('navigation.results')); ?> trouvé(s)</div>
    </div>
    <div class="stat-card">
        <span class="stat-value"><?php echo e($detections->where('deadline', '>', now())->count()); ?></span>
        <div class="stat-label">Encore ouvertes</div>
    </div>
    <div class="stat-card">
        <span class="stat-value"><?php echo e($detections->whereNotNull('amount')->count()); ?></span>
        <div class="stat-label">Avec montant</div>
    </div>
    <div class="stat-card">
        <span class="stat-value"><?php echo e(round($detections->avg('score'))); ?>%</span>
        <div class="stat-label">Score moyen</div>
    </div>
</div>

<!-- Filtres et recherche -->
<div class="card search-filters">
    <form method="get" action="<?php echo e(route('detections.index')); ?>" class="input-group" style="margin-bottom: 1rem;">
        <span class="input-icon">🔍</span>
        <input class="input search-input" type="search" name="search" value="<?php echo e(request('search')); ?>" 
               placeholder="<?php echo e(__('navigation.search')); ?>..." style="flex: 1;">
        <input class="input" type="number" name="min_score" value="<?php echo e(request('min_score')); ?>" 
               placeholder="Score min" min="0" max="100" style="width: 120px;">
        <button class="btn btn-primary" type="submit"><?php echo e(__('navigation.filter')); ?></button>
    </form>
    
    <!-- Filtres rapides -->
    <div class="chips">
        <span class="chip <?php echo e(request('country') == 'France' ? 'is-active' : ''); ?>" 
              onclick="toggleFilter('country', 'France')">🇫🇷 France</span>
        <span class="chip <?php echo e(request('country') == 'Canada' ? 'is-active' : ''); ?>" 
              onclick="toggleFilter('country', 'Canada')">🇨🇦 Canada</span>
        <span class="chip <?php echo e(request('country') == 'USA' ? 'is-active' : ''); ?>" 
              onclick="toggleFilter('country', 'USA')">🇺🇸 USA</span>
        <span class="chip <?php echo e(request('level') == 'Master' ? 'is-active' : ''); ?>" 
              onclick="toggleFilter('level', 'Master')">🎓 Master</span>
        <span class="chip <?php echo e(request('level') == 'Doctorat' ? 'is-active' : ''); ?>" 
              onclick="toggleFilter('level', 'Doctorat')">📚 Doctorat</span>
        <span class="chip <?php echo e(request('language') == 'Français' ? 'is-active' : ''); ?>" 
              onclick="toggleFilter('language', 'Français')">🇫🇷 Français</span>
        <span class="chip <?php echo e(request('language') == 'English' ? 'is-active' : ''); ?>" 
              onclick="toggleFilter('language', 'English')">🇬🇧 English</span>
    </div>
</div>

<!-- Tableau des bourses -->
<div class="card">
    <table class="table" data-datatable>
        <thead>
            <tr>
                <th class="th-sortable" data-type="string"><?php echo e(__('navigation.title')); ?></th>
                <th class="th-sortable" data-type="string"><?php echo e(__('navigation.country')); ?></th>
                <th class="th-sortable" data-type="string"><?php echo e(__('navigation.level')); ?></th>
                <th class="th-sortable" data-type="string"><?php echo e(__('navigation.language_req')); ?></th>
                <th class="th-sortable" data-type="string"><?php echo e(__('navigation.amount')); ?></th>
                <th class="th-sortable" data-type="string"><?php echo e(__('navigation.deadline')); ?></th>
                <th class="th-sortable" data-type="number"><?php echo e(__('navigation.score')); ?></th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $detections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr class="searchable-card">
                <td style="max-width: 400px;">
                    <div style="font-weight: 600; margin-bottom: 4px;">
                        <a href="<?php echo e($d->item_url); ?>" target="_blank" rel="noopener" style="color: var(--text);">
                            <?php echo e(e($d->title)); ?>

                        </a>
                    </div>
                    <div style="color: var(--muted); font-size: var(--font-sm);">
                        <?php echo e(e($d->source_name)); ?>

                    </div>
                </td>
                <td>
                    <span class="badge"><?php echo e(e($d->country)); ?></span>
                </td>
                <td>
                    <span class="badge badge-scholarship"><?php echo e(e($d->level)); ?></span>
                </td>
                <td><?php echo e(e($d->language)); ?></td>
                <td>
                    <?php if($d->amount): ?>
                        <span style="font-weight: 600; color: var(--accent);"><?php echo e(e($d->amount)); ?></span>
                    <?php else: ?>
                        <span style="color: var(--muted);">Non spécifié</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if($d->deadline): ?>
                        <?php
                            $deadline = \Illuminate\Support\Carbon::parse($d->deadline);
                            $isUrgent = $deadline->diffInDays(now()) <= 30;
                        ?>
                        <span class="badge <?php echo e($isUrgent ? 'badge-danger' : 'badge-primary'); ?>">
                            <?php echo e($deadline->isoFormat('DD MMM YYYY')); ?>

                        </span>
                        <?php if($isUrgent): ?>
                            <div style="font-size: var(--font-xs); color: var(--danger); margin-top: 2px;">
                                ⚠️ <?php echo e($deadline->diffForHumans()); ?>

                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <span style="color: var(--muted);">—</span>
                    <?php endif; ?>
                </td>
                <td>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <div style="flex: 1; height: 4px; background: rgba(255,255,255,0.1); border-radius: 2px; overflow: hidden;">
                            <div style="height: 100%; background: linear-gradient(90deg, var(--danger), var(--warning), var(--success)); width: <?php echo e($d->score); ?>%; border-radius: 2px;"></div>
                        </div>
                        <span style="font-weight: 700; min-width: 35px;"><?php echo e($d->score); ?>%</span>
                    </div>
                </td>
                <td>
                    <div style="display: flex; gap: 8px;">
                        <a href="<?php echo e($d->item_url); ?>" target="_blank" rel="noopener" class="btn btn-primary">
                            🔗 <?php echo e(__('navigation.apply_now')); ?>

                        </a>
                        <?php if($d->deadline && \Illuminate\Support\Carbon::parse($d->deadline)->isFuture()): ?>
                            <button class="btn" onclick="addToFavorites('<?php echo e($d->id); ?>')" title="Ajouter aux favoris">
                                ⭐
                            </button>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</div>

<!-- Pagination -->
<?php if($detections->hasPages()): ?>
<nav class="pagination">
    
    <?php if($detections->onFirstPage()): ?>
        <span class="btn" style="opacity: 0.5; cursor: not-allowed;">‹ Précédent</span>
    <?php else: ?>
        <a class="btn" href="<?php echo e($detections->previousPageUrl()); ?>" rel="prev">‹ Précédent</a>
    <?php endif; ?>

    
    <?php
        $start = max(1, $detections->currentPage() - 2);
        $end = min($detections->lastPage(), $detections->currentPage() + 2);
    ?>

    <?php if($start > 1): ?>
        <a class="btn" href="<?php echo e($detections->url(1)); ?>">1</a>
        <?php if($start > 2): ?>
            <span class="btn" style="opacity: 0.5;">...</span>
        <?php endif; ?>
    <?php endif; ?>

    <?php for($i = $start; $i <= $end; $i++): ?>
        <?php if($i == $detections->currentPage()): ?>
            <span class="btn btn-primary"><?php echo e($i); ?></span>
        <?php else: ?>
            <a class="btn" href="<?php echo e($detections->url($i)); ?>"><?php echo e($i); ?></a>
        <?php endif; ?>
    <?php endfor; ?>

    <?php if($end < $detections->lastPage()): ?>
        <?php if($end < $detections->lastPage() - 1): ?>
            <span class="btn" style="opacity: 0.5;">...</span>
        <?php endif; ?>
        <a class="btn" href="<?php echo e($detections->url($detections->lastPage())); ?>"><?php echo e($detections->lastPage()); ?></a>
    <?php endif; ?>

    
    <?php if($detections->hasMorePages()): ?>
        <a class="btn" href="<?php echo e($detections->nextPageUrl()); ?>" rel="next">Suivant ›</a>
    <?php else: ?>
        <span class="btn" style="opacity: 0.5; cursor: not-allowed;">Suivant ›</span>
    <?php endif; ?>
</nav>
<?php endif; ?>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
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
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/ebola/MILANDOU_Divine/teps/bourse/laravel-app/resources/views/detections/index.blade.php ENDPATH**/ ?>