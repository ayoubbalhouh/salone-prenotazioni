<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Closure;
use App\Models\Service;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;

/**
 * Calcola gli orari liberi per un servizio in un certo giorno,
 * tenendo conto di orari di apertura, pausa pranzo, chiusure e appuntamenti già presi.
 */
class SlotFinder
{
    public function isOpen(CarbonInterface $date): bool
    {
        return isset(config('salone.hours')[$date->dayOfWeekIso])
            && ! Closure::whereDate('date', $date->toDateString())->exists();
    }

    /** I prossimi giorni in cui si può prenotare. */
    public function openDays(): array
    {
        $days = [];
        $today = CarbonImmutable::today();
        for ($i = 0; $i < config('salone.days_ahead'); $i++) {
            $day = $today->addDays($i);
            if ($this->isOpen($day)) {
                $days[] = $day;
            }
        }

        return $days;
    }

    /** @return string[] orari liberi nel formato "H:i" */
    public function availableSlots(Service $service, CarbonInterface $date): array
    {
        $date = CarbonImmutable::parse($date->toDateString());
        if (! $this->isOpen($date) || $date->gt(CarbonImmutable::today()->addDays(config('salone.days_ahead') - 1))) {
            return [];
        }

        [$open, $close] = config('salone.hours')[$date->dayOfWeekIso];
        $start = $date->setTimeFromTimeString($open);
        $end = $date->setTimeFromTimeString($close);
        [$lunchFrom, $lunchTo] = config('salone.lunch_break');
        $lunchFrom = $date->setTimeFromTimeString($lunchFrom);
        $lunchTo = $date->setTimeFromTimeString($lunchTo);
        $earliest = CarbonImmutable::now()->addMinutes(config('salone.min_notice'));

        $taken = Booking::active()
            ->whereBetween('starts_at', [$start->startOfDay(), $start->endOfDay()])
            ->get(['starts_at', 'ends_at']);

        $slots = [];
        for ($t = $start; $t->addMinutes($service->duration)->lte($end); $t = $t->addMinutes(config('salone.slot_step'))) {
            $tEnd = $t->addMinutes($service->duration);

            if ($t->lt($earliest)) continue;
            if ($t->lt($lunchTo) && $tEnd->gt($lunchFrom)) continue;

            $overlaps = $taken->contains(fn ($b) => $t->lt($b->ends_at) && $tEnd->gt($b->starts_at));
            if (! $overlaps) {
                $slots[] = $t->format('H:i');
            }
        }

        return $slots;
    }

    public function isAvailable(Service $service, CarbonInterface $startsAt): bool
    {
        return in_array($startsAt->format('H:i'), $this->availableSlots($service, $startsAt), true);
    }
}
