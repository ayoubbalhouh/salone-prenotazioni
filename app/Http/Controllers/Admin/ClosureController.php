<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Closure;
use Illuminate\Http\Request;

class ClosureController extends Controller
{
    public function index()
    {
        return view('admin.closures.index', [
            'closures' => Closure::whereDate('date', '>=', today())->orderBy('date')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'date' => ['required', 'date', 'after_or_equal:today', 'unique:closures,date'],
            'reason' => ['nullable', 'string', 'max:80'],
        ], ['date.unique' => 'Questo giorno è già chiuso.']);

        Closure::create($data);

        $conflicts = Booking::where('status', Booking::CONFIRMED)->whereDate('starts_at', $data['date'])->count();

        return back()->with('status', $conflicts
            ? "Giorno chiuso. Attenzione: ci sono {$conflicts} prenotazioni da spostare."
            : 'Giorno chiuso.');
    }

    public function destroy(Closure $closure)
    {
        $closure->delete();

        return back()->with('status', 'Chiusura rimossa.');
    }
}
