<!DOCTYPE html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}" class="theme-cyan">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ isset($title) ? $title . ' - ' . config('app.name') : config('app.name') }}</title>
    
    @vite(['resources/css/futuristic.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-content">
                <a href="{{ route('detections.index') }}" class="brand">
                    🌐 {{ config('app.name') }}
                </a>
                
                <div class="nav-links">
                    <a href="{{ route('detections.index') }}" class="{{ request()->routeIs('detections.*') ? 'active' : '' }}">
                        {{ __('navigation.scholarships') }}
                    </a>
                    <a href="{{ route('articles.index') }}" class="{{ request()->routeIs('articles.*') ? 'active' : '' }}">
                        {{ __('navigation.orientation') }}
                    </a>
                    <a href="{{ route('careers.index') }}" class="{{ request()->routeIs('careers.*') ? 'active' : '' }}">
                        {{ __('navigation.careers') }}
                    </a>
                </div>
                
                <div class="actions">
                    <div class="lang-switcher">
                        <a href="{{ url()->current() }}?lang=fr" class="{{ app()->getLocale() == 'fr' ? 'active' : '' }}">FR</a>
                        <a href="{{ url()->current() }}?lang=en" class="{{ app()->getLocale() == 'en' ? 'active' : '' }}">EN</a>
                    </div>
                    <button class="btn btn-primary" onclick="window.FUI?.toggleTheme()">
                        🎨 Thème
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <main class="container" style="padding-top:20px;">
        @yield('content')
    </main>

    <footer style="margin-top: 4rem; padding: 2rem 0; border-top: 1px solid var(--stroke);">
        <div class="container">
            <div class="grid grid-4">
                <div>
                    <h5 style="margin-bottom: 1rem; color: var(--text);">{{ config('app.name') }}</h5>
                    <p style="color: var(--muted);">Plateforme d'aide à l'orientation et aux bourses d'études.</p>
                </div>
                <div>
                    <h6 style="margin-bottom: 1rem; color: var(--text);">Navigation</h6>
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <a href="{{ route('detections.index') }}" style="color: var(--muted);">{{ __('navigation.scholarships') }}</a>
                        <a href="{{ route('articles.index') }}" style="color: var(--muted);">{{ __('navigation.orientation') }}</a>
                        <a href="{{ route('careers.index') }}" style="color: var(--muted);">{{ __('navigation.careers') }}</a>
                    </div>
                </div>
                <div>
                    <h6 style="margin-bottom: 1rem; color: var(--text);">Catégories</h6>
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <a href="{{ route('articles.category', 'orientation') }}" style="color: var(--muted);">Orientation</a>
                        <a href="{{ route('articles.category', 'etudes') }}" style="color: var(--muted);">Études</a>
                        <a href="{{ route('articles.category', 'conseils') }}" style="color: var(--muted);">Conseils</a>
                    </div>
                </div>
                <div>
                    <h6 style="margin-bottom: 1rem; color: var(--text);">{{ __('navigation.contact') }}</h6>
                    <p style="color: var(--muted); margin: 0;">contact@bourses.com</p>
                </div>
            </div>
            <div style="text-align: center; margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--stroke); color: var(--muted);">
                <p style="margin: 0;">&copy; {{ date('Y') }} {{ config('app.name') }}. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                window.FUI?.toast('{{ session('success') }}', 'success');
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                window.FUI?.toast('{{ session('error') }}', 'error');
            });
        </script>
    @endif

    <div id="toast-root" aria-live="polite" aria-atomic="true" style="position: fixed; right: 16px; bottom: 16px; display: flex; flex-direction: column; gap: 10px; z-index: 200;"></div>
    
    @stack('scripts')
</body>
</html>
