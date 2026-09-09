<?php

namespace App\Console\Commands;

use App\Models\SystemAddons;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\View;

/**
 * Keeps the storefront theme picker honest: a `theme_N` add-on is only left
 * activated when the matching Blade view `front.template-N.index` actually
 * exists on disk. The shipped script advertises 15 themes but only ships one,
 * so selecting themes 3-15 silently falls back to template-1 (HomeController).
 *
 * Deactivating the ghosts is fully reversible — build `front/template-N/` and
 * re-run this command and slot N lights back up. Nothing is deleted.
 */
class SyncRealThemes extends Command
{
    protected $signature = 'themes:sync-real {--dry-run : List what would change without saving}';

    protected $description = 'Activate only theme add-ons that have a real template view on disk; hide the rest.';

    public function handle(): int
    {
        $themes = SystemAddons::where('unique_identifier', 'like', 'theme_%')->get();
        if ($themes->isEmpty()) {
            $this->warn('No theme_* add-ons found.');
            return self::SUCCESS;
        }

        $dry = (bool) $this->option('dry-run');
        $activated = [];
        $hidden = [];

        foreach ($themes as $theme) {
            $n = str_replace('theme_', '', $theme->unique_identifier);
            $viewExists = View::exists('front.template-' . $n . '.index');
            $shouldBe = $viewExists ? 1 : 0;

            if ((int) $theme->activated !== $shouldBe) {
                if (!$dry) {
                    $theme->activated = $shouldBe;
                    $theme->save();
                }
            }

            if ($viewExists) {
                $activated[] = $n;
            } else {
                $hidden[] = $n;
            }
        }

        sort($activated, SORT_NUMERIC);
        sort($hidden, SORT_NUMERIC);

        $this->info(($dry ? '[dry-run] ' : '') . 'Real themes kept active (have a view): ' . (implode(', ', $activated) ?: 'none'));
        $this->line('Ghost themes hidden (no view on disk): ' . (implode(', ', $hidden) ?: 'none'));
        $this->newLine();
        $this->line('Build resources/views/front/template-N/index.blade.php and re-run to re-enable slot N.');

        return self::SUCCESS;
    }
}
