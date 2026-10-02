<?php

namespace App\Models;

use App\Casts\SafeEncrypted;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * WhatsApp Cloud API configuration for one business.
 * vendor_id 1 is the Order Click platform number.
 */
class WhatsappSetting extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_settings';
    protected $guarded = [];

    // The access token is a permanent credential — never store it in the clear.
    protected $casts = [
        'access_token' => SafeEncrypted::class,
        'app_secret'   => SafeEncrypted::class,
    ];

    public static function forVendor($vendorId): self
    {
        $row = static::firstOrCreate(
            ['vendor_id' => $vendorId],
            ['verify_token' => 'oc_' . Str::random(32), 'api_version' => 'v21.0', 'is_active' => 2, 'ai_enabled' => 1]
        );

        if (empty($row->verify_token)) {
            $row->verify_token = 'oc_' . Str::random(32);
            $row->save();
        }

        return $row;
    }

    /**
     * Whether the WhatsApp Cloud tools are switched on (config('services.whatsapp_cloud.tools')).
     * Off for every account, super admin included, until WHATSAPP_TOOLS=true is set.
     */
    public static function toolsEnabledFor($vendorId = null): bool
    {
        return (bool) config('services.whatsapp_cloud.tools');
    }

    /** Which business owns the number Meta just delivered a message to. */
    public static function byPhoneNumberId(?string $phoneNumberId): ?self
    {
        if (empty($phoneNumberId)) {
            return null;
        }

        return static::where('phone_number_id', $phoneNumberId)->first();
    }

    public function isReady(): bool
    {
        return (int) $this->is_active === 1
            && !empty($this->phone_number_id)
            && !empty($this->access_token);
    }

    /** The Callback URL the client pastes into Meta. */
    public function callbackUrl(): string
    {
        return url('webhook/whatsapp');
    }
}
