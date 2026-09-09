<?php

namespace App\Models;

use App\Helpers\Systems;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorBranch extends Model
{
    use HasFactory;

    protected $table = 'vendor_branches';
    protected $guarded = [];
    protected $casts = [
        'opening_hours' => 'array',
        'fulfilment'    => 'array',
    ];

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    /**
     * Marketplace visibility is DERIVED, never a manual switch — this is what makes the client's
     * "the business must not be entered into the Marketplace a second time" rule hold. A branch
     * becomes publicly discoverable when, and only when, all of these are true:
     *
     *   · the branch is active
     *   · its location review has passed
     *   · the account itself is approved (paid + website activated)
     *   · it has a GPS pin, OR it is an online/remote provider that does not need one
     */
    public function isMarketplaceVisible($vendor = null): bool
    {
        $vendor = $vendor ?: $this->vendor;

        if ((int) $this->is_available !== 1 || $this->review_status !== 'verified') {
            return false;
        }
        if (!Systems::isLive($vendor)) {
            return false;
        }

        return (int) $this->is_remote === 1 || (!empty($this->latitude) && !empty($this->longitude));
    }

    /** Short reason the branch is not visible yet, for the admin Locations table. */
    public function marketplaceState($vendor = null): array
    {
        $vendor = $vendor ?: $this->vendor;

        if ($this->isMarketplaceVisible($vendor)) {
            return ['text' => 'Live in Marketplace', 'class' => 'bg-success'];
        }
        if ((int) $this->is_available !== 1) {
            return ['text' => 'Branch inactive', 'class' => 'bg-secondary'];
        }
        if ($this->review_status === 'rejected') {
            return ['text' => 'Location rejected', 'class' => 'bg-danger'];
        }
        if ((int) $this->is_remote !== 1 && (empty($this->latitude) || empty($this->longitude))) {
            return ['text' => 'GPS missing', 'class' => 'bg-danger'];
        }
        if ($this->review_status !== 'verified') {
            return ['text' => 'Pending approval', 'class' => 'bg-warning'];
        }

        return ['text' => 'Live after approval', 'class' => 'bg-warning'];
    }

    /** GPS state badge for the admin table. */
    public function gpsState(): array
    {
        if ((int) $this->is_remote === 1) {
            return ['text' => 'Online / remote', 'class' => 'text-muted'];
        }
        if (empty($this->latitude) || empty($this->longitude)) {
            return ['text' => 'Missing', 'class' => 'text-danger'];
        }
        if ($this->review_status === 'verified') {
            return ['text' => 'Verified', 'class' => 'text-success'];
        }

        return ['text' => 'Review needed', 'class' => 'text-warning'];
    }

    /** Human coverage summary, phrased per system. */
    public function coverageLabel(?string $system = null): string
    {
        $system = Systems::normalise($system ?: optional($this->vendor)->system);

        if ((int) $this->is_remote === 1) {
            return 'Online / no travel';
        }
        if (empty($this->coverage_km)) {
            return $system === Systems::BOOKING ? 'At branch' : '—';
        }

        $km = rtrim(rtrim(number_format($this->coverage_km, 2), '0'), '.');

        return $system === Systems::ORDERS
            ? $km . ' km delivery'
            : ($system === Systems::SERVICE ? $km . ' km on-site' : $km . ' km');
    }
}
