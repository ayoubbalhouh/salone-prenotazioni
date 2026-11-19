@extends('layouts.app')

@section('content')
<section class="hero">
  <div class="container hero-inner">
    <div>
      <p class="eyebrow">Barbiere &amp; parrucchiere · Padova</p>
      <h1>Il tuo taglio,<br><em>prenotato in 30 secondi.</em></h1>
      <p class="lead">Scegli il servizio, il giorno e l'orario che preferisci. Ti mandiamo subito la conferma via email. Niente telefonate, niente attese.</p>
      <div class="hero-actions">
        <a href="#servizi" class="btn">Prenota ora</a>
        <a href="#orari" class="btn btn-ghost">Orari e indirizzo</a>
      </div>
    </div>
    <div class="hero-card" aria-hidden="true">
      <div class="hc-head"><span>Prossimo appuntamento</span><span class="pill">Confermato</span></div>
      <p class="hc-service">Taglio + barba</p>
      <p class="hc-when">Sabato · 10:30</p>
      <div class="hc-steps">
        <span class="on">1 · Servizio</span><span class="on">2 · Orario</span><span class="on">3 · Fatto ✓</span>
      </div>
    </div>
  </div>
</section>

<section id="servizi" class="section">
  <div class="container">
    <div class="section-head">
      <p class="eyebrow">Servizi e prezzi</p>
      <h2>Cosa possiamo fare per te</h2>
    </div>
    <div class="services">
      @foreach ($services as $service)
        <a href="{{ route('booking.create', $service) }}" class="service">
          <div>
            <h3>{{ $service->name }}</h3>
            <p>{{ $service->description }}</p>
          </div>
          <div class="service-side">
            <span class="price">{{ $service->price }}</span>
            <span class="muted small">{{ $service->duration }} min</span>
            <span class="service-cta">Prenota →</span>
          </div>
        </a>
      @endforeach
    </div>
  </div>
</section>

<section id="orari" class="section alt">
  <div class="container info">
    <div>
      <p class="eyebrow">Orari</p>
      <h2>Ti aspettiamo</h2>
      <ul class="hours">
        @foreach ([1 => 'Lunedì', 2 => 'Martedì', 3 => 'Mercoledì', 4 => 'Giovedì', 5 => 'Venerdì', 6 => 'Sabato', 7 => 'Domenica'] as $n => $label)
          @php($h = config('salone.hours')[$n] ?? null)
          <li class="{{ now()->dayOfWeekIso === $n ? 'today' : '' }}"><span>{{ $label }}</span><span>{{ $h ? $h[0].' – '.$h[1] : 'Chiuso' }}</span></li>
        @endforeach
      </ul>
      <p class="muted small">Pausa pranzo {{ implode('–', config('salone.lunch_break')) }}</p>
    </div>
    <div class="info-card">
      <h3>Dove siamo</h3>
      <p>{{ config('salone.address') }}</p>
      <p>{{ config('salone.phone') }}</p>
      <a href="#servizi" class="btn">Prenota un appuntamento</a>
    </div>
  </div>
</section>
@endsection
