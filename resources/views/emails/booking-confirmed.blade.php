<!doctype html>
<html lang="it">
<body style="margin:0;background:#f7f4ef;font-family:Arial,Helvetica,sans-serif;color:#1c1a17">
  <table width="100%" cellpadding="0" cellspacing="0" style="padding:32px 16px">
    <tr><td align="center">
      <table width="100%" cellpadding="0" cellspacing="0" style="max-width:520px;background:#ffffff;border-radius:16px;padding:32px">
        <tr><td>
          <p style="font-size:22px;margin:0 0 4px"><strong>{{ config('salone.name') }}</strong></p>
          <p style="color:#6b645c;margin:0 0 24px">Prenotazione confermata</p>
          <p>Ciao {{ $booking->customer_name }}, ti aspettiamo!</p>
          <table width="100%" style="background:#f7f4ef;border-radius:12px;padding:16px;margin:16px 0">
            <tr><td style="color:#6b645c">Servizio</td><td align="right"><strong>{{ $booking->service->name }}</strong></td></tr>
            <tr><td style="color:#6b645c">Quando</td><td align="right"><strong>{{ ucfirst($booking->starts_at->translatedFormat('l j F')) }}, {{ $booking->starts_at->format('H:i') }}</strong></td></tr>
            <tr><td style="color:#6b645c">Prezzo</td><td align="right"><strong>{{ $booking->service->price }}</strong></td></tr>
            <tr><td style="color:#6b645c">Codice</td><td align="right"><strong>{{ $booking->code }}</strong></td></tr>
          </table>
          <p><a href="{{ route('booking.show', $booking) }}" style="display:inline-block;background:#1c1a17;color:#fff;text-decoration:none;padding:12px 20px;border-radius:999px">Vedi o annulla la prenotazione</a></p>
          <p style="color:#6b645c;font-size:13px">{{ config('salone.address') }} · {{ config('salone.phone') }}</p>
        </td></tr>
      </table>
    </td></tr>
  </table>
</body>
</html>
