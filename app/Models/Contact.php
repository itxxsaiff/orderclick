<?php

namespace App\Models;

use App\Helpers\helper;
use App\Helpers\Systems;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * A customer / visitor inquiry from a contact or support form.
 *
 * Inquiries arrive automatically from the website — there is no "add" action. Archiving replaces
 * deletion so a conversation is never lost.
 */
class Contact extends Model
{
    use HasFactory;

    public const STATUS_NEW = 'new';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_RESOLVED = 'resolved';

    public function products()
    {
        return $this->hasOne('App\Models\Item', 'id', 'product_id');
    }

    /** The account this inquiry came from, when the sender is registered. */
    public function linkedUser()
    {
        return $this->belongsTo(User::class, 'linked_user_id');
    }

    public static function typeOptions(): array
    {
        return [
            'general'      => 'General',
            'registration' => 'Registration',
            'payment'      => 'Payment',
            'technical'    => 'Technical Support',
        ];
    }

    public static function statusOptions(): array
    {
        return [
            self::STATUS_NEW         => 'New',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_RESOLVED    => 'Resolved',
        ];
    }

    /** Related system options; blank is valid for a general inquiry. */
    public static function systemOptions(): array
    {
        $out = ['' => '—'];
        foreach (Systems::all() as $s) {
            $out[$s['key']] = $s['name'];
        }

        return $out;
    }

    public function typeLabel(): string
    {
        return self::typeOptions()[$this->inquiry_type ?? 'general'] ?? 'General';
    }

    public function statusLabel(): string
    {
        return self::statusOptions()[$this->status ?? self::STATUS_NEW] ?? 'New';
    }

    /** Green when resolved, amber while in progress, neutral when new. */
    public function statusClass(): string
    {
        return [
            self::STATUS_NEW         => 'bg-secondary',
            self::STATUS_IN_PROGRESS => 'bg-warning',
            self::STATUS_RESOLVED    => 'bg-success',
        ][$this->status ?? self::STATUS_NEW] ?? 'bg-secondary';
    }

    public function systemLabel(): string
    {
        return empty($this->related_system) ? '—' : Systems::label($this->related_system);
    }

    public function isArchived(): bool
    {
        return !empty($this->archived_at);
    }

    /** mailto: link pre-filled with a reply. */
    public function replyEmailUrl(): string
    {
        $subject = rawurlencode('Re: your inquiry — ' . (helper::appdata('')->website_title ?? 'Order Click'));
        $body = rawurlencode("\n\n---\n" . trans('labels.your_message') . ":\n" . $this->message);

        return 'mailto:' . $this->email . '?subject=' . $subject . '&body=' . $body;
    }

    /** wa.me link, when the sender left a phone number. */
    public function replyWhatsappUrl(): ?string
    {
        $number = preg_replace('/[^0-9]/', '', (string) $this->mobile);
        if ($number === '') {
            return null;
        }

        return 'https://wa.me/' . $number . '?text=' . rawurlencode(
            trans('messages.whatsapp_reply_intro') . ' ' . $this->name
        );
    }

    /**
     * Match an inquiry to a registered account by email — a vendor or a customer. General
     * inquiries from an unknown address stay unlinked.
     */
    public static function linkToAccount(string $email): ?int
    {
        $user = User::where('email', trim(strtolower($email)))->where('is_deleted', 2)->first();

        return $user->id ?? null;
    }
}
