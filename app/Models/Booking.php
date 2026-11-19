<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasFactory;

    public const CONFIRMED = 'confermata';
    public const COMPLETED = 'completata';
    public const CANCELLED = 'annullata';

    protected $fillable = ['service_id', 'customer_name', 'email', 'phone', 'notes', 'starts_at', 'ends_at', 'status'];

    protected function casts(): array
    {
        return ['starts_at' => 'datetime', 'ends_at' => 'datetime'];
    }

    protected static function booted(): void
    {
        static::creating(function (Booking $booking) {
            do {
                $code = Str::upper(Str::random(6));
            } while (static::where('code', $code)->exists());
            $booking->code ??= $code;
        });
    }

    public function getRouteKeyName(): string
    {
        return 'code';
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    // Prenotazioni che occupano la poltrona
    public function scopeActive($query)
    {
        return $query->where('status', '!=', self::CANCELLED);
    }

    public function isCancellable(): bool
    {
        return $this->status === self::CONFIRMED && $this->starts_at->isFuture();
    }
}
