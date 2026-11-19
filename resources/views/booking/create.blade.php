@extends('layouts.app')

@section('title', 'Prenota '.$service->name.' · '.config('salone.name'))

@section('content')
<section class="section booking">
  <div class="container narrow">
    <a href="{{ route('home') }}#servizi" class="back">← Cambia servizio</a>

    <div class="summary-card">
      <div>
        <p class="eyebrow">Stai prenotando</p>
        <h1>{{ $service->name }}</h1>
        <p class="muted">{{ $service->description }}</p>
      </div>
      <div class="summary-side">
        <span class="price">{{ $service->price }}</span>
        <span class="muted small">{{ $service->duration }} minuti</span>
      </div>
    </div>

    @if (! $selected)
      <p class="alert">Al momento non ci sono giorni disponibili. Riprova più tardi.</p>
    @else
    <form method="POST" action="{{ route('booking.store', $service) }}" class="booking-form" id="bookingForm" novalidate>
      @csrf
      <input type="hidden" name="data" id="dateInput" value="{{ old('data', $selected->toDateString()) }}">

      <fieldset>
        <legend><span class="step">1</span> Scegli il giorno</legend>
        <div class="days" id="days">
          @foreach ($days as $day)
            <a href="{{ route('booking.create', [$service, 'data' => $day->toDateString()]) }}"
               class="day {{ $day->isSameDay($selected) ? 'active' : '' }}"
               data-date="{{ $day->toDateString() }}">
              <span class="dow">{{ $day->isToday() ? 'Oggi' : ($day->isTomorrow() ? 'Domani' : $day->translatedFormat('D')) }}</span>
              <strong>{{ $day->format('j') }}</strong>
              <span class="mon">{{ $day->translatedFormat('M') }}</span>
            </a>
          @endforeach
        </div>
      </fieldset>

      <fieldset>
        <legend><span class="step">2</span> Scegli l'orario <span class="muted small" id="dayLabel">· {{ $selected->translatedFormat('l j F') }}</span></legend>
        <div class="times" id="times">
          @forelse ($times as $time)
            <label class="time"><input type="radio" name="ora" value="{{ $time }}" {{ old('ora') === $time ? 'checked' : '' }} required><span>{{ $time }}</span></label>
          @empty
            <p class="muted">Nessun orario libero in questo giorno. Prova un altro giorno.</p>
          @endforelse
        </div>
        @error('ora') <p class="error">{{ $message }}</p> @enderror
      </fieldset>

      <fieldset>
        <legend><span class="step">3</span> I tuoi dati</legend>
        <div class="grid-2">
          <label>Nome e cognome
            <input name="nome" value="{{ old('nome') }}" required autocomplete="name" placeholder="Mario Rossi">
            @error('nome') <span class="error">{{ $message }}</span> @enderror
          </label>
          <label>Telefono
            <input name="telefono" type="tel" value="{{ old('telefono') }}" required autocomplete="tel" placeholder="333 123 4567">
            @error('telefono') <span class="error">{{ $message }}</span> @enderror
          </label>
        </div>
        <label><span>Email <span class="muted">(ti mandiamo la conferma)</span></span>
          <input name="email" type="email" value="{{ old('email') }}" required autocomplete="email" placeholder="mario@email.it">
          @error('email') <span class="error">{{ $message }}</span> @enderror
        </label>
        <label><span>Note <span class="muted">(facoltativo)</span></span>
          <textarea name="note" rows="2" placeholder="Es. sfumatura bassa, barba corta…">{{ old('note') }}</textarea>
        </label>
      </fieldset>

      <button class="btn btn-block" type="submit">Conferma prenotazione</button>
      <p class="muted small center">Puoi annullare gratis dal link che ricevi via email.</p>
    </form>
    @endif
  </div>
</section>
@endsection

@push('scripts')
<script>
// Cambia giorno senza ricaricare la pagina
(() => {
  const days = document.getElementById('days');
  if (!days) return;
  const times = document.getElementById('times');
  const dateInput = document.getElementById('dateInput');
  const dayLabel = document.getElementById('dayLabel');
  const url = @json(route('booking.times', $service));

  days.addEventListener('click', async (e) => {
    const link = e.target.closest('.day');
    if (!link) return;
    e.preventDefault();
    days.querySelectorAll('.day').forEach((d) => d.classList.toggle('active', d === link));
    dateInput.value = link.dataset.date;
    history.replaceState(null, '', link.href);
    dayLabel.textContent = '· ' + new Date(link.dataset.date + 'T12:00').toLocaleDateString('it-IT', { weekday: 'long', day: 'numeric', month: 'long' });
    times.classList.add('loading');

    try {
      const res = await fetch(url + '?data=' + link.dataset.date, { headers: { Accept: 'application/json' } });
      const data = await res.json();
      times.innerHTML = data.times.length
        ? data.times.map((t) => `<label class="time"><input type="radio" name="ora" value="${t}" required><span>${t}</span></label>`).join('')
        : '<p class="muted">Nessun orario libero in questo giorno. Prova un altro giorno.</p>';
    } catch {
      window.location = link.href;
    } finally {
      times.classList.remove('loading');
    }
  });
})();
</script>
@endpush
