<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use App\Services\SlotFinder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(['email' => 'admin@studioforbice.test'], [
            'name' => 'Admin',
            'password' => 'password',
        ]);

        $services = collect([
            ['Taglio uomo', 'Shampoo, taglio a forbice o macchinetta, styling', 30, 1800],
            ['Taglio + barba', 'Il nostro classico: taglio completo e rifinitura barba', 45, 2800],
            ['Barba', 'Rifinitura con panno caldo e rasoio', 20, 1200],
            ['Taglio bambino', 'Fino a 12 anni', 20, 1300],
            ['Taglio donna', 'Consulenza, shampoo, taglio e piega', 45, 3500],
            ['Piega', 'Shampoo e piega', 30, 2000],
            ['Colore', 'Colore completo con prodotti senza ammoniaca', 90, 5500],
        ])->map(fn ($s, $i) => Service::create([
            'name' => $s[0], 'description' => $s[1], 'duration' => $s[2], 'price_cents' => $s[3], 'position' => $i,
        ]));

        // Qualche prenotazione di esempio nei prossimi giorni
        $finder = app(SlotFinder::class);
        $names = ['Luca Bianchi', 'Giulia Rossi', 'Marco Ferrari', 'Sara Romano', 'Davide Conti', 'Elena Greco', 'Andrea Costa', 'Chiara Bruno', 'Matteo Gallo', 'Francesca Leone'];
        $n = 0;
        foreach (array_slice($finder->openDays(), 0, 6) as $day) {
            foreach (range(1, rand(2, 4)) as $_) {
                $service = $services->random();
                $slots = $finder->availableSlots($service, $day);
                if (! $slots) {
                    continue;
                }
                $start = $day->setTimeFromTimeString($slots[array_rand($slots)]);
                $name = $names[$n++ % count($names)];
                Booking::create([
                    'service_id' => $service->id,
                    'customer_name' => $name,
                    'email' => strtolower(str_replace(' ', '.', $name)).'@example.com',
                    'phone' => '333 '.rand(100, 999).' '.rand(1000, 9999),
                    'notes' => rand(0, 3) === 0 ? 'Sfumatura bassa, grazie' : null,
                    'starts_at' => $start,
                    'ends_at' => $start->addMinutes($service->duration),
                ]);
            }
        }
    }
}
