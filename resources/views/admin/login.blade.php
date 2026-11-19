<!doctype html>
<html lang="it">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex">
<title>Accesso · {{ config('salone.name') }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="login-page">
  <form method="POST" action="{{ url('/admin/login') }}" class="login-card">
    @csrf
    <a href="{{ route('home') }}" class="logo">Studio <em>Forbice</em></a>
    <h1>Area riservata</h1>
    <label>Email<input name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username"></label>
    <label>Password<input name="password" type="password" required autocomplete="current-password"></label>
    @error('email') <p class="error">{{ $message }}</p> @enderror
    <label class="check"><input type="checkbox" name="remember"> Ricordami</label>
    <button class="btn btn-block">Entra</button>
    <p class="muted small center">Demo: admin@studioforbice.test · password</p>
  </form>
</body>
</html>
