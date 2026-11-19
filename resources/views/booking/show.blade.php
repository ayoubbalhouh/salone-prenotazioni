@extends('layouts.app')

@section('title', 'Prenotazione '.$booking->code.' · '.config('salone.name'))

@section('content')
<section class="section booking">
  <div class="container narrow">
    <div class="confirm-card status-{{ $booking->status }}">
      @if ($booking->status === \App\Models\Booking::CANCELLED)
        <div class="badge-icon cancel">×</div>
        <h1>Prenotazione annullata</h1>
        <p class="muted">Nessun problema. Quando vuoi, puoi prenotare un nuovo appuntamento.</p>
      @else
        <div class="badge-icon">✓</div>
        <h1>{{ session('created') ? 'Ci vediamo presto!' : 'La tua prenotazione' }}</h1>
        @if (session('created'))
          <p class="muted">Abbiamo inviato la conferma a <strong>{{ $booking->email }}</strong>.</p>
        @endif
      @endif

      <dl class="details">
        <div><dt>Servizio</dt><dd>{{ $booking->service->name }}</dd></div>
        <div><dt>Quando</dt><dd>{{ ucfirst($booking->starts_at->translatedFormat('l j F')) }} · {{ $booking->starts_at->format('H:i') }}</dd></div>
        <div><dt>Durata</dt><dd>{{ $booking->service->duration }} minuti</dd></div>
        <div><dt>Prezzo</dt><dd>{{ $booking->service->price }} <span class="muted small">(si paga in salone)</span></dd></div>
        <div><dt>Codice</dt><dd class="code">{{ $booking->code }}</dd></div>
      </dl>

      <div class="confirm-actions">
        @if ($booking->isCancellable())
          <form method="POST" action="{{ route('booking.cancel', $booking) }}" onsubmit="return confirm('Vuoi davvero annullare la prenotazione?')">
            @csrf
            <button class="btn btn-ghost" type="submit">Annulla prenotazione</button>
          </form>
        @endif
        <a href="{{ route('home') }}" class="btn">Torna alla home</a>
      </div>
      <p class="muted small">{{ config('salone.address') }}</p>
    </div>
  </div>
</section>
@endsection
