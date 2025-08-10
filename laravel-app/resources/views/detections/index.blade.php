@extends('layouts.app')

@section('title', 'Opportunités | Bourses')

@section('navbar-search')
<form class="d-flex" role="search" method="get" action="{{ route('detections.index') }}">
  <input class="form-control me-2" type="search" name="search" value="{{ request('search') }}" placeholder="Recherche...">
  <input class="form-control me-2" type="number" name="min_score" value="{{ request('min_score') }}" placeholder="Score min" min="0" max="100">
  <button class="btn btn-outline-primary" type="submit">Filtrer</button>
</form>
@endsection

@section('content')
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
          @foreach ($detections as $d)
          <tr>
            <td style="max-width: 520px;">
              <div class="fw-semibold truncate"><a href="{{ $d->item_url }}" target="_blank" rel="noopener">{{ e($d->title) }}</a></div>
              <div class="small text-muted">{{ e($d->source_name) }}</div>
            </td>
            <td>{{ e($d->country) }}</td>
            <td>{{ e($d->level) }}</td>
            <td>{{ e($d->language) }}</td>
            <td>{{ e($d->amount) }}</td>
            <td>
              @if($d->deadline)
                <span class="badge text-bg-primary">{{ \Illuminate\Support\Carbon::parse($d->deadline)->isoFormat('DD MMM YYYY') }}</span>
              @else
                <span class="muted">—</span>
              @endif
            </td>
            <td class="text-end"><span class="fw-bold">{{ $d->score }}</span></td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  <div class="card-footer bg-white">
    {{ $detections->links() }}
  </div>
</div>
@endsection
