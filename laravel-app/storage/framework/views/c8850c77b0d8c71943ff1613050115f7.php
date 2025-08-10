<?php $__env->startSection('title', 'Opportunités | Bourses'); ?>

<?php $__env->startSection('navbar-search'); ?>
<form class="d-flex" role="search" method="get" action="<?php echo e(route('detections.index')); ?>">
  <input class="form-control me-2" type="search" name="search" value="<?php echo e(request('search')); ?>" placeholder="Recherche...">
  <input class="form-control me-2" type="number" name="min_score" value="<?php echo e(request('min_score')); ?>" placeholder="Score min" min="0" max="100">
  <button class="btn btn-outline-primary" type="submit">Filtrer</button>
</form>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>Titre</th>
            <th class="text-nowrap">Pays</th>
            <th>Niveau</th>
            <th>Langue</th>
            <th>Montant</th>
            <th class="text-nowrap">Échéance</th>
            <th class="text-end">Score</th>
          </tr>
        </thead>
        <tbody>
          <?php $__currentLoopData = $detections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <tr>
            <td style="max-width: 520px;">
              <div class="fw-semibold truncate"><a href="<?php echo e($d->item_url); ?>" target="_blank" rel="noopener"><?php echo e(e($d->title)); ?></a></div>
              <div class="small text-muted"><?php echo e(e($d->source_name)); ?></div>
            </td>
            <td><?php echo e(e($d->country)); ?></td>
            <td><?php echo e(e($d->level)); ?></td>
            <td><?php echo e(e($d->language)); ?></td>
            <td><?php echo e(e($d->amount)); ?></td>
            <td>
              <?php if($d->deadline): ?>
                <span class="badge text-bg-primary"><?php echo e(\Illuminate\Support\Carbon::parse($d->deadline)->isoFormat('DD MMM YYYY')); ?></span>
              <?php else: ?>
                <span class="muted">—</span>
              <?php endif; ?>
            </td>
            <td class="text-end"><span class="fw-bold"><?php echo e($d->score); ?></span></td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>
    </div>
  </div>
  <div class="card-footer bg-white">
    <?php echo e($detections->links()); ?>

  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/ebola/MILANDOU_Divine/teps/bourse/laravel-app/resources/views/detections/index.blade.php ENDPATH**/ ?>