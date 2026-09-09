<?php

namespace App\Models;

use App\Helpers\Systems;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * A template image — a screenshot of a real storefront template, classified by system and by the
 * activities it suits.
 *
 * Templates are created and published from the admin panel only. Nothing else, AI included, may
 * add or publish one; the AI layer may only RECOMMEND from what is already approved here.
 */
class Theme extends Model
{
    use HasFactory;

    protected $table = 'theme';

    /** Activities this template is for. Empty means every activity in its system. */
    public function activityIds(): array
    {
        return array_values(array_filter(explode('|', (string) $this->activity_ids)));
    }

    public function appliesToActivity($activityId): bool
    {
        $ids = $this->activityIds();

        return empty($ids) || in_array((string) $activityId, $ids, true);
    }

    public function systemLabel(): string
    {
        return Systems::label($this->system);
    }

    /** Human list of applicable activities, for the admin table. */
    public function activityLabel(): string
    {
        $ids = $this->activityIds();
        if (empty($ids)) {
            return 'All ' . Systems::label($this->system) . ' activities';
        }

        return Activity::whereIn('id', $ids)->pluck('name')->implode(', ') ?: '—';
    }

    /** Does the template image actually exist on disk yet? Screenshots are uploaded later. */
    public function hasPreview(): bool
    {
        return !empty($this->image)
            && file_exists(storage_path('app/public/admin-assets/images/theme/' . $this->image));
    }

    /**
     * Templates a merchant may choose from: their purchased system, narrowed to their activity.
     *
     * If the activity has no dedicated template, the system's general templates (those tagged for
     * all activities) are returned, so the design section is never empty.
     */
    public static function forVendor($vendor)
    {
        $system = Systems::normalise($vendor->system ?? null);

        $inSystem = static::where('system', $system)->orderBy('reorder_id')->get();

        if (empty($vendor->activity_id)) {
            return $inSystem->values();
        }

        $matched = $inSystem->filter(fn($t) => $t->appliesToActivity($vendor->activity_id))->values();

        return $matched->isNotEmpty()
            ? $matched
            : $inSystem->filter(fn($t) => empty($t->activityIds()))->values();
    }

    /**
     * The single best template for a system + activity. This is the ONLY thing the AI layer may
     * use — it picks from approved templates and never creates or publishes one.
     */
    public static function recommend($system, $activityId = null): ?self
    {
        $pool = static::where('system', Systems::normalise($system))->orderBy('reorder_id')->get();

        // An activity-specific template beats a general one.
        $exact = $pool->first(fn($t) => $activityId && in_array((string) $activityId, $t->activityIds(), true));

        return $exact ?: $pool->first(fn($t) => empty($t->activityIds())) ?: $pool->first();
    }
}
