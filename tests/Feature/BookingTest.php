<?php

namespace Tests\Feature;

use App\Mail\BookingConfirmed;
use App\Models\Booking;
use App\Models\Closure;
use App\Models\Service;
use App\Models\User;
use App\Services\SlotFinder;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    private Service $service;

    protected function setUp(): void
    {
        parent::setUp();
        // Lunedì 16 novembre 2026, ore 08:00 (il salone è chiuso il lunedì)
        $this->travelTo(CarbonImmutable::parse('2026-11-16 08:00'));
        $this->service = Service::create(['name' => 'Taglio uomo', 'duration' => 30, 'price_cents' => 1800]);
    }

    public function test_home_lists_active_services(): void
    {
        Service::create(['name' => 'Servizio nascosto', 'duration' => 30, 'price_cents' => 1000, 'active' => false]);

        $this->get('/')->assertOk()->assertSee('Taglio uomo')->assertDontSee('Servizio nascosto');
    }

    public function test_slots_respect_hours_and_lunch_break(): void
    {
        $slots = app(SlotFinder::class)->availableSlots($this->service, CarbonImmutable::parse('2026-11-17'));

        $this->assertSame('09:00', $slots[0]);
        $this->assertSame('18:30', end($slots));
        $this->assertContains('12:30', $slots);
        $this->assertNotContains('12:45', $slots); // finirebbe dopo le 13:00
        $this->assertNotContains('13:30', $slots);
        $this->assertContains('14:00', $slots);
    }

    public function test_no_slots_on_closed_days(): void
    {
        $finder = app(SlotFinder::class);
        $this->assertSame([], $finder->availableSlots($this->service, CarbonImmutable::parse('2026-11-16'))); // lunedì

        Closure::create(['date' => '2026-11-18']);
        $this->assertSame([], $finder->availableSlots($this->service, CarbonImmutable::parse('2026-11-18')));
    }

    public function test_customer_can_book_and_receives_email(): void
    {
        Mail::fake();

        $response = $this->post("/prenota/{$this->service->id}", [
            'data' => '2026-11-17', 'ora' => '10:00',
            'nome' => 'Mario Rossi', 'email' => 'mario@example.com', 'telefono' => '333 1234567',
        ]);

        $booking = Booking::first();
        $response->assertRedirect("/prenotazione/{$booking->code}");
        $this->assertEquals('2026-11-17 10:30:00', $booking->ends_at->toDateTimeString());
        Mail::assertSent(BookingConfirmed::class, fn ($m) => $m->hasTo('mario@example.com'));
    }

    public function test_the_same_slot_cannot_be_booked_twice(): void
    {
        Mail::fake();
        $data = ['data' => '2026-11-17', 'ora' => '10:00', 'nome' => 'A', 'email' => 'a@example.com', 'telefono' => '333 1234567'];

        $this->post("/prenota/{$this->service->id}", $data);
        $this->post("/prenota/{$this->service->id}", $data)->assertSessionHasErrors('ora');

        $this->assertSame(1, Booking::count());
        $slots = app(SlotFinder::class)->availableSlots($this->service, CarbonImmutable::parse('2026-11-17'));
        $this->assertNotContains('09:45', $slots); // si sovrapporrebbe
        $this->assertContains('10:30', $slots);
    }

    public function test_customer_can_cancel_and_the_slot_is_free_again(): void
    {
        $booking = Booking::create([
            'service_id' => $this->service->id, 'customer_name' => 'A', 'email' => 'a@example.com', 'phone' => '333 1234567',
            'starts_at' => '2026-11-17 10:00', 'ends_at' => '2026-11-17 10:30',
        ]);

        $this->post("/prenotazione/{$booking->code}/annulla")->assertRedirect();

        $this->assertSame(Booking::CANCELLED, $booking->fresh()->status);
        $this->assertContains('10:00', app(SlotFinder::class)->availableSlots($this->service, CarbonImmutable::parse('2026-11-17')));
    }

    public function test_admin_area_requires_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');

        $user = User::factory()->create();
        $this->actingAs($user)->get('/admin')->assertOk()->assertSee('Agenda');
    }

    public function test_admin_can_create_a_service(): void
    {
        $this->actingAs(User::factory()->create())->post('/admin/servizi', [
            'name' => 'Trattamento barba', 'duration' => 25, 'price' => '15.50', 'active' => '1',
        ])->assertRedirect('/admin/servizi');

        $this->assertDatabaseHas('services', ['name' => 'Trattamento barba', 'price_cents' => 1550, 'active' => true]);
    }
}
