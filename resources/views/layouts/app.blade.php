<!doctype html>
<html lang="it">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title', config('salone.name').' · Barbiere e parrucchiere a Padova')</title>
<meta name="description" content="Prenota online il tuo taglio da {{ config('salone.name') }}, Padova. Scegli servizio, giorno e orario in meno di un minuto.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<header class="site-header">
  <div class="container header-inner">
    <a href="{{ route('home') }}" class="logo">Studio <em>Forbice</em></a>
    <nav class="nav">
      <a href="{{ route('home') }}#servizi">Servizi</a>
      <a href="{{ route('home') }}#orari">Orari</a>
      <a href="{{ route('home') }}#servizi" class="btn btn-sm">Prenota</a>
    </nav>
  </div>
</header>

<main>
  @yield('content')
</main>

<footer class="site-footer">
  <div class="container footer-inner">
    <div>
      <a href="{{ route('home') }}" class="logo">Studio <em>Forbice</em></a>
      <p>{{ config('salone.address') }} · {{ config('salone.phone') }}</p>
    </div>
    <p class="muted small">Progetto demo di <strong>Ayoub Balhouh</strong> · Laravel {{ app()->version() }} · <a href="{{ route('login') }}">Area riservata</a></p>
  </div>
</footer>
@stack('scripts')
</body>
</html>
