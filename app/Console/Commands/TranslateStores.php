<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\StoreTranslator;
use Illuminate\Console\Command;

/**
 * Translate store content (services, products, categories, FAQs, description, AI design texts)
 * into every website language.
 *
 *   php artisan store:translate 64          one store (vendor id)
 *   php artisan store:translate --all       every store
 *   php artisan store:translate 64 --fresh  forget old translations first
 */
class TranslateStores extends Command
{
    protected $signature = 'store:translate {vendor? : vendor (store owner) id} {--all : every store} {--fresh : translate everything again}';

    protected $description = 'Translate store content into all website languages with AI';

    public function handle(): int
    {
        if ($this->option('all')) {
            $ids = User::where('type', 2)->where('is_deleted', 2)->orderBy('id')->pluck('id')->all();
        } elseif ($this->argument('vendor')) {
            $ids = [(int) $this->argument('vendor')];
        } else {
            $this->error('Give a vendor id or --all.');

            return self::FAILURE;
        }

        foreach ($ids as $vid) {
            if ($this->option('fresh')) {
                StoreTranslator::reset($vid);
            }
            $left = StoreTranslator::pending($vid)['total'];
            if ($left === 0) {
                $this->line("Store {$vid}: nothing to translate.");
                continue;
            }
            $this->info("Store {$vid}: {$left} to translate…");
            $stuck = 0;
            while ($left > 0 && $stuck < 3) {
                $r = StoreTranslator::step($vid);
                if (empty($r['success'])) {
                    $this->warn('  ' . ($r['error'] ?? 'failed') . ' — retrying');
                    $stuck++;
                    continue;
                }
                $stuck = $r['remaining'] < $left ? 0 : $stuck + 1;
                $left = $r['remaining'];
                $this->line("  {$left} left");
            }
            $this->info($left === 0 ? "Store {$vid}: done." : "Store {$vid}: stopped with {$left} left (run again).");
        }

        return self::SUCCESS;
    }
}
