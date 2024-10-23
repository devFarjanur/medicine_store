<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'address_id',
        'order_number',
        'total_price',
        'payment_method',
        'status',
        'payment_status',
        'transaction_id',
        'currency',
    ];

    const STATUS_PENDING = 'Pending';
    const STATUS_PROCESSING = 'Processing';
    const STATUS_SHIPPED = 'Shipped';
    const STATUS_DELIVERED = 'Delivered';
    const STATUS_CANCELLED = 'Cancelled';
    const STATUS_RETURNED = 'Returned';

    const PAYMENT_PENDING = 'pending_payment';
    const PAYMENT_DUE = 'payment_due';
    const PAYMENT_PAID = 'paid';
    const PAYMENT_FAILED = 'payment_failed';
    const PAYMENT_REFUNDED = 'refunded';

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    // Use model events to delete related order items
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($order) {
            // Delete all related order items before deleting the order
            $order->items()->delete();
        });
    }

}
