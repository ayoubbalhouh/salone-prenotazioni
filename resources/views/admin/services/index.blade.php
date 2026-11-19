@extends('layouts.admin')

@section('title', 'Servizi')

@section('content')
<div class="page-head">
  <div><p class="eyebrow">Listino</p><h1>Servizi</h1></div>
  <a href="{{ route('admin.services.create') }}" class="btn">+ Nuovo servizio</a>
</div>

<div class="table-wrap">
  <table class="table">
    <thead><tr><th>Servizio</th><th>Durata</th><th>Prezzo</th><th>Prenotazioni</th><th>Stato</th><th></th></tr></thead>
    <tbody>
      @foreach ($services as $s)
        <tr class="{{ $s->active ? '' : 'inactive' }}">
          <td><strong>{{ $s->name }}</strong><br><span class="muted small">{{ $s->description }}</span></td>
          <td>{{ $s->duration }} min</td>
          <td>{{ $s->price }}</td>
          <td>{{ $s->bookings_count }}</td>
          <td><span class="pill {{ $s->active ? '' : 'off' }}">{{ $s->active ? 'Attivo' : 'Nascosto' }}</span></td>
          <td class="row-actions">
            <a href="{{ route('admin.services.edit', $s) }}" class="btn btn-ghost btn-sm">Modifica</a>
            <form method="POST" action="{{ route('admin.services.destroy', $s) }}" onsubmit="return confirm('Eliminare {{ $s->name }}?')">@csrf @method('DELETE')<button class="link">Elimina</button></form>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection
