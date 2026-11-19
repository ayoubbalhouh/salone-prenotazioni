<?php

// Orari di apertura del salone. Chiave = giorno della settimana (1 = lunedì ... 7 = domenica).
return [
    'name' => 'Studio Forbice',
    'address' => 'Via Altinate 21, 35121 Padova',
    'phone' => '049 000 0000',

    'hours' => [
        2 => ['09:00', '19:00'],
        3 => ['09:00', '19:00'],
        4 => ['09:00', '19:00'],
        5 => ['09:00', '20:00'],
        6 => ['08:30', '17:00'],
    ],

    'lunch_break' => ['13:00', '14:00'],

    // Ogni quanti minuti parte un appuntamento
    'slot_step' => 15,

    // Preavviso minimo per prenotare (minuti)
    'min_notice' => 60,

    // Quanti giorni in avanti si può prenotare
    'days_ahead' => 21,
];
