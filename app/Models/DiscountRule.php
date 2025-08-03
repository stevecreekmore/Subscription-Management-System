<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscountRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'type',
        'min_subscriptions',
        'min_total_spent',
        'discount_type',
        'discount_value',
        'max_discount_amount',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'min_total_spent' => 'decimal:2',
            'discount_value' => 'decimal:2',
            'max_discount_amount' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function calculateDiscount(User $user, float $originalPrice): float
    {
        if (!$this->is_active) {
            return 0;
        }

        $qualifies = match ($this->type) {
            'subscription_count' => $user->getActiveSubscriptionsCount() >= $this->min_subscriptions,
            'total_spent' => $user->total_spent >= $this->min_total_spent,
            'both' => $user->getActiveSubscriptionsCount() >= $this->min_subscriptions && 
                     $user->total_spent >= $this->min_total_spent,
            default => false,
        };

        if (!$qualifies) {
            return 0;
        }

        $discount = match ($this->discount_type) {
            'percentage' => $originalPrice * ($this->discount_value / 100),
            'fixed_amount' => $this->discount_value,
            default => 0,
        };

        if ($this->max_discount_amount && $discount > $this->max_discount_amount) {
            $discount = $this->max_discount_amount;
        }

        return min($discount, $originalPrice);
    }
}
