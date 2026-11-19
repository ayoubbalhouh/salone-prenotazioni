<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $day = rescue(fn () => CarbonImmutable::createFromFormat('Y-m-d', $request->query('data'))->startOfDay(), CarbonImmutable::today(), false);

        $bookings = Booking::with('service')
            ->whereBetween('starts_at', [$day, $day->endOfDay()])
            ->orderBy('starts_at')
            ->get();

        $weekStart = $day->startOfWeek();
        $week = collect(range(0, 6))->map(fn ($i) => $weekStart->addDays($i));
        $weekCounts = Booking::active()
            ->whereBetween('starts_at', [$weekStart, $weekStart->addDays(6)->endOfDay()])
            ->get(['starts_at'])
            ->countBy(fn ($b) => $b->starts_at->toDateString());

        $active = $bookings->where('status', '!=', Booking::CANCELLED);

        return view('admin.dashboard', [
            'day' => $day,
            'bookings' => $bookings,
            'week' => $week,
            'weekCounts' => $weekCounts,
            'stats' => [
                'count' => $active->count(),
                'revenue' => $active->sum(fn ($b) => $b->service->price_cents),
                'next' => Booking::active()->with('service')->where('status', Booking::CONFIRMED)->where('starts_at', '>=', now())->orderBy('starts_at')->first(),
                'upcoming' => Booking::where('status', Booking::CONFIRMED)->where('starts_at', '>=', now())->count(),
            ],
        ]);
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $request->validate(['status' => ['required', Rule::in([Booking::CONFIRMED, Booking::COMPLETED, Booking::CANCELLED])]]);
        $booking->update(['status' => $request->status]);

        return back()->with('status', 'Prenotazione aggiornata.');
    }
}
