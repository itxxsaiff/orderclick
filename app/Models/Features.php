<?php

namespace App\Models;

use App\Helpers\Systems;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * A landing-page feature card. Marketing content only — no vendor workflow, no AI execution.
 */
class Features extends Model
{
    use HasFactory;

    protected $table = 'features';

    /** Applies-To options for the picker. */
    public static function appliesToOptions(): array
    {
        $out = ['all' => 'All Systems'];
        foreach (Systems::all() as $s) {
            $out[$s['key']] = $s['name'];
        }

        return $out;
    }

    public function appliesToLabel(): string
    {
        $key = $this->applies_to ?: 'all';

        return $key === 'all' ? 'All Systems' : Systems::label($key);
    }

    /** Colour for the Applies To badge — green for general, neutral for a specific system. */
    public function appliesToClass(): string
    {
        return ($this->applies_to ?: 'all') === 'all' ? 'bg-success' : 'bg-secondary';
    }

    public function hasImage(): bool
    {
        return !empty($this->image)
            && file_exists(storage_path('app/public/admin-assets/images/feature/' . $this->image));
    }

    /** Features for one system: 'all' returns only the general ones. */
    public static function forSystem(string $system)
    {
        return static::where('vendor_id', 1)
            ->where('applies_to', $system)
            ->orderBy('reorder_id')->orderBy('id')
            ->get();
    }
}
