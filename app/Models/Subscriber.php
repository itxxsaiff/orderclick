<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * A marketing email subscriber.
 *
 * Addresses are only ever added when someone explicitly subscribes — vendor and customer accounts
 * are never harvested into this list. Unsubscribing sets a status; it never deletes the row, so
 * the address stays on record and no future marketing email can reach it.
 */
class Subscriber extends Model
{
    use HasFactory;

    protected $table = 'subscribers';
    protected $guarded = [];

    public const SUBSCRIBED = 'subscribed';
    public const UNSUBSCRIBED = 'unsubscribed';

    public function isSubscribed(): bool
    {
        return ($this->status ?: self::SUBSCRIBED) === self::SUBSCRIBED;
    }

    public function statusLabel(): string
    {
        return $this->isSubscribed() ? 'Subscribed' : 'Unsubscribed';
    }

    public function statusClass(): string
    {
        return $this->isSubscribed() ? 'bg-success' : 'bg-secondary';
    }

    /** One-click unsubscribe link for the footer of a marketing email. */
    public function unsubscribeUrl(): string
    {
        return url('unsubscribe/' . $this->token);
    }

    public function unsubscribe(): void
    {
        $this->status = self::UNSUBSCRIBED;
        $this->unsubscribed_at = now();
        $this->save();
    }

    public function resubscribe(): void
    {
        $this->status = self::SUBSCRIBED;
        $this->subscribed_at = now();
        $this->unsubscribed_at = null;
        $this->save();
    }

    /**
     * Record an explicit subscription.
     *
     * Re-subscribing an address that previously opted out flips it back rather than creating a
     * duplicate row, and the same address is never stored twice for one vendor.
     */
    public static function subscribeEmail($vendorId, string $email, string $source = 'signup_form'): ?self
    {
        $email = trim(strtolower($email));
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return null;
        }

        $subscriber = static::where('vendor_id', $vendorId)->where('email', $email)->first();

        if ($subscriber) {
            $subscriber->resubscribe();
            return $subscriber;
        }

        return static::create([
            'vendor_id'     => $vendorId,
            'email'         => $email,
            'status'        => self::SUBSCRIBED,
            'token'         => Str::random(48),
            'subscribed_at' => now(),
            'source'        => $source,
        ]);
    }

    /**
     * The only list a marketing send may use. Anything not explicitly subscribed is excluded.
     */
    public static function mailingList($vendorId)
    {
        return static::where('vendor_id', $vendorId)
            ->where('status', self::SUBSCRIBED)
            ->orderBy('email')
            ->get();
    }
}
