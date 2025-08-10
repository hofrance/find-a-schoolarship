<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Bourses')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif; }
        a { text-decoration: none; }
        .muted { color: #6c757d; }
        .truncate { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    </style>
    @stack('head')
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg bg-white shadow-sm">
  <div class="container-fluid">
    <a class="navbar-brand" href="{{ url('/') }}">Bourses</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav" aria-controls="nav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link {{ request()->routeIs('detections.index') ? 'active' : '' }}" href="{{ route('detections.index') }}">Opportunités</a></li>
        {{-- Placeholders for future pages --}}
        {{-- <li class="nav-item"><a class="nav-link" href="#">Universités</a></li> --}}
      </ul>
      @yield('navbar-search')
    </div>
  </div>
</nav>

<main class="container py-4">
  @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
