<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'customer_name',
        'customer_email',
        'total',
    ];

    protected function casts(): array
    {
        return [
            'total' => 'integer',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Generate a unique order number with format: MS-YYMMDD-XXXXXX
     */
    public static function generateOrderNumber(): string
    {
        $prefix = 'MS-'.date('ymd').'-';
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // Unambiguous uppercase alphanumeric

        do {
            $random = '';
            for ($i = 0; $i < 6; $i++) {
                $random .= $chars[random_int(0, strlen($chars) - 1)];
            }
            $orderNumber = $prefix.$random;
        } while (static::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }
}
