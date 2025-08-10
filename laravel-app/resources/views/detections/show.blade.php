@extends('layouts.app')

@section('title', e($detection->title))

@section('content')
  <a href="{{ route('detections.index') }}" class="btn btn-link">← Retour</a>
  <div class="card">
    <div class="card-body">
      <h1 class="h4">{{ e($detection->title) }}</h1>
      <p class="text-muted mb-1"><strong>Source:</strong> {{ e($detection->source_name) }}</p>
      <p class="text-muted mb-1"><strong>Pays:</strong> {{ e($detection->country) }} | <strong>Niveau:</strong> {{ e($detection->level) }} | <strong>Langue:</strong> {{ e($detection->language) }}</p>
      <p class="text-muted mb-1"><strong>Montant:</strong> {{ e($detection->amount) }} | <strong>Échéance:</strong> {{ $detection->deadline ? \Illuminate\Support\Carbon::parse($detection->deadline)->toFormattedDateString() : '—' }}</p>
      <p class="text-muted"><strong>Score:</strong> {{ $detection->score }}</p>
      @if($detection->summary)
      <p>{{ e($detection->summary) }}</p>
      @endif
      <a href="{{ $detection->item_url }}" target="_blank" class="btn btn-primary">Ouvrir le lien</a>
    </div>
  </div>
@endsection
