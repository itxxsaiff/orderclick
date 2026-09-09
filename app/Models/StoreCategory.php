<?php

namespace App\Models;

use App\Helpers\helper;
use App\Helpers\Systems;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreCategory extends Model
{
    use HasFactory;

    protected $table = 'store_category';

    public function activity()
    {
        return $this->belongsTo(Activity::class, 'activity_id');
    }

    public function systemLabel(): string
    {
        return Systems::label($this->system);
    }

    /**
     * The marketplace category for a given activity — this is what registration assigns, so the
     * merchant never picks a category by hand.
     *
     * Falls back to the "Other" category of the activity's system, so a vendor always lands
     * somewhere sensible rather than nowhere.
     */
    public static function forActivity($activityId): ?self
    {
        $activity = Activity::find($activityId);
        if (empty($activity)) {
            return null;
        }

        $candidates = static::where('activity_id', $activity->id)
            ->where('is_deleted', 2)->where('is_available', 1)
            ->orderBy('reorder_id')->orderBy('id')
            ->get();

        if ($candidates->isEmpty()) {
            return static::otherFor($activity->system);
        }

        // Several categories can share one activity (Restaurants, Cafés and Bakeries all sit under
        // "Restaurants & Cafés"). The category named exactly after the activity is the
        // representative one, so it always wins; otherwise fall back to word overlap.
        $exact = $candidates->firstWhere('name', $activity->name);
        if ($exact) {
            return $exact;
        }

        $words = fn(string $name) => array_filter(
            preg_split('/[^\p{L}\p{N}]+/u', mb_strtolower($name)),
            fn($w) => mb_strlen($w) > 2
        );
        $activityWords = $words($activity->name);

        $best = null;
        $bestScore = 0;
        foreach ($candidates as $candidate) {
            $score = count(array_intersect($words($candidate->name), $activityWords));
            if ($score > $bestScore) {
                $bestScore = $score;
                $best = $candidate;
            }
        }

        return $best ?: $candidates->first();
    }

    /** The catch-all category for a system. */
    public static function otherFor(?string $system): ?self
    {
        return static::where('system', Systems::normalise($system))
            ->where('is_other', 1)->where('is_deleted', 2)
            ->first();
    }

    /**
     * Category artwork. Deliberately separate from a vendor's logo or cover image — those belong
     * to the business, these label the marketplace section, and one must never stand in for the
     * other. Returns null when no image has been uploaded yet so the view can show a placeholder.
     */
    public function imageUrl(): ?string
    {
        return empty($this->image) ? null : helper::image_path($this->image);
    }
}
