<?php

namespace App\Console\Commands;

use App\Models\SystemAddons;
use App\Models\Theme;
use Illuminate\Console\Command;

/**
 * Registers the in-house "Classic" storefront theme (template-2) so it becomes
 * selectable by vendors (Settings > Theme) and assignable to plans by the admin.
 *
 * The whole theme gate is a single `systemaddons` row keyed by `unique_identifier`.
 * Both the vendor theme picker and the plan editor build their theme list from
 * SystemAddons rows matching `theme_%`, so inserting `theme_2` is all that is
 * required to surface template-2 everywhere. Idempotent — safe to run repeatedly.
 */
class RegisterClassicTheme extends Command
{
    protected $signature = 'themes:register-classic';

    protected $description = 'Register the in-house Classic theme (template-2) so vendors and plans can use it.';

    public function handle(): int
    {
        // 1) The gate: a systemaddons row so the picker + plan editor list it.
        $addon = SystemAddons::where('unique_identifier', 'theme_2')->first();
        if (empty($addon)) {
            SystemAddons::create([
                'name' => 'Classic',
                'unique_identifier' => 'theme_2',
                'version' => '1.0',
                'activated' => 1,
                'image' => 'theme_2.jpg',
            ]);
            $this->info('Registered "Classic" theme add-on (theme_2) and activated it.');
        } else {
            if ($addon->activated != 1) {
                $addon->activated = 1;
                $addon->save();
                $this->info('Re-activated existing "Classic" theme add-on (theme_2).');
            } else {
                $this->info('"Classic" theme add-on (theme_2) already registered and active.');
            }
        }

        // 2) The catalog row (marketing gallery / plan-builder thumbnail). The
        //    admin can replace name/image later from Admin > Themes.
        $catalog = Theme::where('vendor_id', 1)->where('name', 'Classic')->first();
        if (empty($catalog)) {
            $theme = new Theme();
            $theme->vendor_id = 1;
            $theme->name = 'Classic';
            $theme->image = 'theme-2.webp';
            $theme->reorder_id = (int) (Theme::where('vendor_id', 1)->max('reorder_id')) + 1;
            $theme->save();
            $this->info('Added "Classic" row to the theme catalog (Admin > Themes).');
        } else {
            $this->info('Theme catalog already has a "Classic" row.');
        }

        $this->newLine();
        $this->info('Done. template-2 (Classic) is now selectable. Replace the thumbnail');
        $this->line('  storage/app/public/admin-assets/images/theme/theme-2.png');
        $this->line('with your own screenshot when ready.');

        return self::SUCCESS;
    }
}
