<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'duration', 'price_cents', 'active', 'position'];

    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true)->orderBy('position')->orderBy('name');
    }

    public function getPriceAttribute(): string
    {
        return number_format($this->price_cents / 100, 2, ',', '.').' €';
    }
}
