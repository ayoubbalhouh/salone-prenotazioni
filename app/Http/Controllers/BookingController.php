<?php

namespace App\Http\Controllers;

use App\Mail\BookingConfirmed;
use App\Models\Booking;
use App\Models\Service;
use App\Services\SlotFinder;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class BookingController extends Controller
{
    public function __construct(private SlotFinder $slots) {}

    public function home()
    {
        return view('home', ['services' => Service::active()->get()]);
    }

    public function create(Request $request, Service $service)
    {
        abort_unless($service->active, 404);

        $days = $this->slots->openDays();
        $selected = collect($days)->first(fn ($d) => $d->toDateString() === $request->query('data'))
            ?? collect($days)->first(fn ($d) => $this->slots->availableSlots($service, $d))
            ?? ($days[0] ?? null);

        return view('booking.create', [
            'service' => $service,
            'days' => $days,
            'selected' => $selected,
            'times' => $selected ? $this->slots->availableSlots($service, $selected) : [],
        ]);
    }

    // Usato dal JavaScript per aggiornare gli orari senza ricaricare la pagina
    public function times(Request $request, Service $service)
    {
        $request->validate(['data' => ['required', 'date_format:Y-m-d']]);

        return response()->json([
            'times' => $this->slots->availableSlots($service, CarbonImmutable::parse($request->query('data'))),
        ]);
    }

    public function store(Request $request, Service $service)
    {
        abort_unless($service->active, 404);

        $data = $request->validate([
            'data' => ['required', 'date_format:Y-m-d'],
            'ora' => ['required', 'date_format:H:i'],
            'nome' => ['required', 'string', 'max:80'],
            'email' => ['required', 'email', 'max:120'],
            'telefono' => ['required', 'regex:/^[0-9 +]{8,20}$/'],
            'note' => ['nullable', 'string', 'max:500'],
        ], [
            'ora.required' => 'Scegli un orario.',
            'telefono.regex' => 'Inserisci un numero di telefono valido.',
        ]);

        $startsAt = CarbonImmutable::parse($data['data'].' '.$data['ora']);

        // Transazione: due clienti non possono prenotare lo stesso orario
        $booking = DB::transaction(function () use ($service, $startsAt, $data) {
            if (! $this->slots->isAvailable($service, $startsAt)) {
                return null;
            }

            return Booking::create([
                'service_id' => $service->id,
                'customer_name' => $data['nome'],
                'email' => $data['email'],
                'phone' => $data['telefono'],
                'notes' => $data['note'] ?? null,
                'starts_at' => $startsAt,
                'ends_at' => $startsAt->addMinutes($service->duration),
            ]);
        });

        if (! $booking) {
            return back()->withInput()->withErrors(['ora' => 'Questo orario non è più disponibile, scegline un altro.']);
        }

        Mail::to($booking->email)->send(new BookingConfirmed($booking));

        return redirect()->route('booking.show', $booking)->with('created', true);
    }

    public function show(Booking $booking)
    {
        return view('booking.show', ['booking' => $booking->load('service')]);
    }

    public function cancel(Booking $booking)
    {
        if ($booking->isCancellable()) {
            $booking->update(['status' => Booking::CANCELLED]);
        }

        return redirect()->route('booking.show', $booking)->with('cancelled', true);
    }
}
