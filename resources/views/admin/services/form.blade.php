@extends('layouts.admin')

@section('title', $service->exists ? 'Modifica servizio' : 'Nuovo servizio')

@section('content')
<div class="page-head">
  <div><a href="{{ route('admin.services.index') }}" class="back">← Servizi</a><h1>{{ $service->exists ? 'Modifica servizio' : 'Nuovo servizio' }}</h1></div>
</div>

<form method="POST" action="{{ $service->exists ? route('admin.services.update', $service) : route('admin.services.store') }}" class="panel-form">
  @csrf
  @if ($service->exists) @method('PUT') @endif
  <label>Nome<input name="name" value="{{ old('name', $service->name) }}" required>@error('name')<span class="error">{{ $message }}</span>@enderror</label>
  <label>Descrizione<input name="description" value="{{ old('description', $service->description) }}"></label>
  <div class="grid-3">
    <label>Durata (minuti)<input name="duration" type="number" min="10" max="240" step="5" value="{{ old('duration', $service->duration) }}" required>@error('duration')<span class="error">{{ $message }}</span>@enderror</label>
    <label>Prezzo (€)<input name="price" type="number" min="0" step="0.5" value="{{ old('price', $service->price_cents !== null ? $service->price_cents / 100 : '') }}" required>@error('price')<span class="error">{{ $message }}</span>@enderror</label>
    <label>Ordine<input name="position" type="number" min="0" value="{{ old('position', $service->position ?? 0) }}"></label>
  </div>
  <label class="check"><input type="checkbox" name="active" value="1" {{ old('active', $service->active) ? 'checked' : '' }}> Visibile sul sito</label>
  <button class="btn">Salva</button>
</form>
@endsection
