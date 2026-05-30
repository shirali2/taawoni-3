<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanupOrphanMenus extends Command
{
    protected $signature   = 'menus:cleanup-orphans';
    protected $description = 'Delete unders rows that reference non-existent menus';

    public function handle()
    {
        $count = DB::table('unders')
            ->whereNotNull('id_menu')
            ->whereNotIn('id_menu', DB::table('menus')->pluck('id'))
            ->count();

        if ($count === 0) {
            $this->info('No orphaned unders found.');
            return 0;
        }

        DB::table('unders')
            ->whereNotNull('id_menu')
            ->whereNotIn('id_menu', DB::table('menus')->pluck('id'))
            ->delete();

        $this->info("Cleaned up {$count} orphaned under record(s).");
        return 0;
    }
}
