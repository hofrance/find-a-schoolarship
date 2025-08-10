<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_','-',app()->getLocale())); ?>" class="theme-cyan">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    
    <title><?php echo e(isset($title) ? $title . ' - ' . config('app.name') : config('app.name')); ?></title>
    
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/futuristic.css', 'resources/js/app.js']); ?>
    <?php echo $__env->yieldPushContent('head'); ?>
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-content">
                <a href="<?php echo e(route('detections.index')); ?>" class="brand">
                    🌐 <?php echo e(config('app.name')); ?>

                </a>
                
                <div class="nav-links">
                    <a href="<?php echo e(route('detections.index')); ?>" class="<?php echo e(request()->routeIs('detections.*') ? 'active' : ''); ?>">
                        <?php echo e(__('navigation.scholarships')); ?>

                    </a>
                    <a href="<?php echo e(route('articles.index')); ?>" class="<?php echo e(request()->routeIs('articles.*') ? 'active' : ''); ?>">
                        <?php echo e(__('navigation.orientation')); ?>

                    </a>
                    <a href="<?php echo e(route('careers.index')); ?>" class="<?php echo e(request()->routeIs('careers.*') ? 'active' : ''); ?>">
                        <?php echo e(__('navigation.careers')); ?>

                    </a>
                    <a href="<?php echo e(route('about')); ?>" class="<?php echo e(request()->routeIs('about') ? 'active' : ''); ?>">
                        <?php echo e(__('navigation.about')); ?>

                    </a>
                </div>
                
                <div class="actions">
                    <div class="lang-switcher">
                        <a href="<?php echo e(url()->current()); ?>?lang=fr" class="<?php echo e(app()->getLocale() == 'fr' ? 'active' : ''); ?>">FR</a>
                        <a href="<?php echo e(url()->current()); ?>?lang=en" class="<?php echo e(app()->getLocale() == 'en' ? 'active' : ''); ?>">EN</a>
                    </div>
                    <button class="btn btn-primary" onclick="window.FUI?.toggleTheme()">
                        🎨 <?php echo e(__('navigation.theme')); ?>

                    </button>
                </div>
            </div>
        </div>
    </nav>

    <main class="container" style="padding-top:20px;">
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <footer style="margin-top: 4rem; padding: 2rem 0; border-top: 1px solid var(--stroke);">
        <div class="container">
            <div class="grid grid-4">
                <div>
                    <h5 style="margin-bottom: 1rem; color: var(--text);"><?php echo e(config('app.name')); ?></h5>
                    <p style="color: var(--muted);">Plateforme d'aide à l'orientation et aux bourses d'études.</p>
                </div>
                <div>
                    <h6 style="margin-bottom: 1rem; color: var(--text);">Navigation</h6>
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <a href="<?php echo e(route('detections.index')); ?>" style="color: var(--muted);"><?php echo e(__('navigation.scholarships')); ?></a>
                        <a href="<?php echo e(route('articles.index')); ?>" style="color: var(--muted);"><?php echo e(__('navigation.orientation')); ?></a>
                        <a href="<?php echo e(route('careers.index')); ?>" style="color: var(--muted);"><?php echo e(__('navigation.careers')); ?></a>
                    </div>
                </div>
                <div>
                    <h6 style="margin-bottom: 1rem; color: var(--text);">Catégories</h6>
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <a href="<?php echo e(route('articles.category', 'orientation')); ?>" style="color: var(--muted);">Orientation</a>
                        <a href="<?php echo e(route('articles.category', 'etudes')); ?>" style="color: var(--muted);">Études</a>
                        <a href="<?php echo e(route('articles.category', 'conseils')); ?>" style="color: var(--muted);">Conseils</a>
                    </div>
                </div>
                <div>
                    <h6 style="margin-bottom: 1rem; color: var(--text);"><?php echo e(__('navigation.contact')); ?></h6>
                    <p style="color: var(--muted); margin: 0;">contact@bourses.com</p>
                </div>
            </div>
            <div style="text-align: center; margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--stroke); color: var(--muted);">
                <p style="margin: 0;">&copy; <?php echo e(date('Y')); ?> <?php echo e(config('app.name')); ?>. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <?php if(session('success')): ?>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                window.FUI?.toast('<?php echo e(session('success')); ?>', 'success');
            });
        </script>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                window.FUI?.toast('<?php echo e(session('error')); ?>', 'error');
            });
        </script>
    <?php endif; ?>

    <div id="toast-root" aria-live="polite" aria-atomic="true" style="position: fixed; right: 16px; bottom: 16px; display: flex; flex-direction: column; gap: 10px; z-index: 200;"></div>
    
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH /home/ebola/MILANDOU_Divine/teps/bourse/laravel-app/resources/views/layouts/app.blade.php ENDPATH**/ ?>