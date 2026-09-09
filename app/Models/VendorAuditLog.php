<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class VendorAuditLog extends Model
{
    use HasFactory;

    protected $table = 'vendor_audit_logs';
    protected $guarded = [];

    /**
     * Record one sensitive change. Called from anywhere a business name, GPS pin, WhatsApp number,
     * plan, verification state or activation is written.
     */
    public static function record($vendorId, string $field, $old, $new, ?string $reason = null, string $actorType = 'admin'): void
    {
        $actor = Auth::user();

        static::create([
            'vendor_id'  => $vendorId,
            'field'      => $field,
            'old_value'  => is_scalar($old) || $old === null ? $old : json_encode($old),
            'new_value'  => is_scalar($new) || $new === null ? $new : json_encode($new),
            'actor_type' => $actorType,
            'actor_id'   => $actor->id ?? null,
            'actor_name' => $actor->name ?? ucfirst($actorType),
            'reason'     => $reason,
            'ip'         => request()->ip(),
        ]);
    }
}
