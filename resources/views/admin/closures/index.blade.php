@extends('layouts.admin')

@section('title', 'Chiusure')

@section('content')
<div class="page-head">
  <div><p class="eyebrow">Ferie e festivi</p><h1>Giorni di chiusura</h1></div>
</div>

<form method="POST" action="{{ route('admin.closures.store') }}" class="panel-form inline-form">
  @csrf
  <label>Giorno<input type="date" name="date" min="{{ today()->toDateString() }}" value="{{ old('date') }}" required></label>
  <label><span>Motivo <span class="muted">(facoltativo)</span></span><input name="reason" value="{{ old('reason') }}" placeholder="Ferie, festività…"></label>
  <button class="btn">Chiudi giorno</button>
  @error('date') <p class="error">{{ $message }}</p> @enderror
</form>

<div class="table-wrap">
  <table class="table">
    <thead><tr><th>Giorno</th><th>Motivo</th><th></th></tr></thead>
    <tbody>
      @forelse ($closures as $c)
        <tr>
          <td><strong>{{ ucfirst($c->date->translatedFormat('l j F Y')) }}</strong></td>
          <td>{{ $c->reason ?: '—' }}</td>
          <td class="row-actions"><form method="POST" action="{{ route('admin.closures.destroy', $c) }}">@csrf @method('DELETE')<button class="link">Riapri</button></form></td>
        </tr>
      @empty
        <tr><td colspan="3" class="muted">Nessuna chiusura programmata.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
@endsection
