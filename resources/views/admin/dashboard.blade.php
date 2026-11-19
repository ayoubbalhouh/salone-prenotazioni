@extends('layouts.admin')

@section('title', 'Agenda')

@section('content')
<div class="page-head">
  <div>
    <p class="eyebrow">Agenda</p>
    <h1>{{ ucfirst($day->translatedFormat('l j F')) }}</h1>
  </div>
  <div class="day-nav">
    <a class="btn btn-ghost btn-sm" href="{{ route('admin.dashboard', ['data' => $day->subDay()->toDateString()]) }}">←</a>
    <a class="btn btn-ghost btn-sm" href="{{ route('admin.dashboard') }}">Oggi</a>
    <a class="btn btn-ghost btn-sm" href="{{ route('admin.dashboard', ['data' => $day->addDay()->toDateString()]) }}">→</a>
  </div>
</div>

<div class="stats">
  <div class="stat"><span>Appuntamenti del giorno</span><strong>{{ $stats['count'] }}</strong></div>
  <div class="stat"><span>Incasso previsto</span><strong>{{ number_format($stats['revenue'] / 100, 2, ',', '.') }} €</strong></div>
  <div class="stat"><span>Prossimi confermati</span><strong>{{ $stats['upcoming'] }}</strong></div>
  <div class="stat"><span>Prossimo cliente</span><strong class="stat-sm">{{ $stats['next'] ? $stats['next']->customer_name.' · '.$stats['next']->starts_at->translatedFormat('D H:i') : '—' }}</strong></div>
</div>

<div class="week">
  @foreach ($week as $d)
    <a href="{{ route('admin.dashboard', ['data' => $d->toDateString()]) }}" class="week-day {{ $d->isSameDay($day) ? 'active' : '' }} {{ $d->isToday() ? 'is-today' : '' }}">
      <span>{{ $d->translatedFormat('D') }}</span>
      <strong>{{ $d->format('j') }}</strong>
      <em>{{ $weekCounts[$d->toDateString()] ?? 0 }}</em>
    </a>
  @endforeach
</div>

<div class="agenda">
  @forelse ($bookings as $b)
    <article class="appt status-{{ $b->status }}">
      <div class="appt-time"><strong>{{ $b->starts_at->format('H:i') }}</strong><span>{{ $b->ends_at->format('H:i') }}</span></div>
      <div class="appt-body">
        <h3>{{ $b->customer_name }} <span class="status">{{ $b->status }}</span></h3>
        <p>{{ $b->service->name }} · {{ $b->service->price }}</p>
        <p class="muted small"><a href="tel:{{ preg_replace('/\s+/', '', $b->phone) }}">{{ $b->phone }}</a> · {{ $b->email }} · #{{ $b->code }}</p>
        @if ($b->notes) <p class="note">“{{ $b->notes }}”</p> @endif
      </div>
      <div class="appt-actions">
        @if ($b->status === \App\Models\Booking::CONFIRMED)
          <form method="POST" action="{{ route('admin.bookings.status', $b) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="completata"><button class="btn btn-sm">✓ Fatto</button></form>
          <form method="POST" action="{{ route('admin.bookings.status', $b) }}" onsubmit="return confirm('Annullare questo appuntamento?')">@csrf @method('PATCH')<input type="hidden" name="status" value="annullata"><button class="btn btn-ghost btn-sm">Annulla</button></form>
        @else
          <form method="POST" action="{{ route('admin.bookings.status', $b) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="confermata"><button class="link">Ripristina</button></form>
        @endif
      </div>
    </article>
  @empty
    <div class="empty-state">Nessun appuntamento in questo giorno.</div>
  @endforelse
</div>
@endsection
