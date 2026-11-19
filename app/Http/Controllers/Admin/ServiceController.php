<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        return view('admin.services.index', [
            'services' => Service::withCount(['bookings' => fn ($q) => $q->active()])->orderBy('position')->orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.services.form', ['service' => new Service(['duration' => 30, 'active' => true])]);
    }

    public function store(Request $request)
    {
        Service::create($this->validated($request));

        return redirect()->route('admin.services.index')->with('status', 'Servizio creato.');
    }

    public function edit(Service $service)
    {
        return view('admin.services.form', ['service' => $service]);
    }

    public function update(Request $request, Service $service)
    {
        $service->update($this->validated($request));

        return redirect()->route('admin.services.index')->with('status', 'Servizio aggiornato.');
    }

    public function destroy(Service $service)
    {
        // Se ha prenotazioni lo disattiviamo, così lo storico resta intatto
        if ($service->bookings()->exists()) {
            $service->update(['active' => false]);

            return back()->with('status', 'Il servizio ha delle prenotazioni: è stato disattivato.');
        }

        $service->delete();

        return back()->with('status', 'Servizio eliminato.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'description' => ['nullable', 'string', 'max:255'],
            'duration' => ['required', 'integer', 'min:10', 'max:240'],
            'price' => ['required', 'numeric', 'min:0', 'max:1000'],
            'position' => ['nullable', 'integer', 'min:0'],
        ]);

        return [
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'duration' => $data['duration'],
            'price_cents' => (int) round($data['price'] * 100),
            'position' => $data['position'] ?? 0,
            'active' => $request->boolean('active'),
        ];
    }
}
