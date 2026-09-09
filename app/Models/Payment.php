<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Casts\SafeEncrypted;

class Payment extends Model
{
    public const TYPE_COD = 1;
    public const TYPE_BANK_TRANSFER = 6;
    public const TYPE_STRIPE = 3;
    public const TYPE_WALLET = 16;
    // V2 manual/offline methods (no gateway keys)
    public const TYPE_CASH = 17;
    public const TYPE_CASH_PICKUP = 18;
    public const TYPE_BENEFITPAY = 19;
    public const TYPE_BANK_QR = 20;
    public const TYPE_PAYMENT_LINK = 21;

    // Manual/offline methods: customer pays outside the app, order stays unpaid until confirmed.
    public const MANUAL_TYPES = [1, 6, 17, 18, 19, 20, 21];

    protected $fillable = [
        'payment_name', 'test_public_key','test_secret_key','live_public_key','live_secret_key','environment','status'
    ];

    // Encrypt real secrets at rest (public_key is a publishable key, left as-is).
    protected $casts = [
        'secret_key' => SafeEncrypted::class,
        'encryption_key' => SafeEncrypted::class,
    ];

    public static function includedPaymentTypes(): array
    {
        return [
            self::TYPE_COD,
            self::TYPE_STRIPE,
            self::TYPE_WALLET,
            self::TYPE_BANK_TRANSFER,
            self::TYPE_CASH,
            self::TYPE_CASH_PICKUP,
            self::TYPE_BENEFITPAY,
            self::TYPE_BANK_QR,
            self::TYPE_PAYMENT_LINK,
        ];

    }

    public function isManual(): bool
    {
        return in_array((int) $this->payment_type, self::MANUAL_TYPES, true);
    }

    public function isIncludedGateway(): bool
    {
        return in_array((int) $this->payment_type, self::includedPaymentTypes(), true);
    }
}
