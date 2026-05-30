<?php

namespace App\Observers;

use App\menu;
use Illuminate\Support\Facades\Cache;

class MenuObserver
{
    public function saved(menu $menu)
    {
        $this->invalidate($menu);
    }

    public function deleted(menu $menu)
    {
        $this->invalidate($menu);
    }

    private function invalidate(menu $menu)
    {
        $raw = $menu->id_grop ?? '';

        $decoded = json_decode($raw, true);
        $gropIds = is_array($decoded) ? $decoded : array_filter(explode(',', $raw));

        foreach ($gropIds as $gropId) {
            $gropId = (int) $gropId;
            if ($gropId > 0) {
                Cache::increment("menu_version_{$gropId}");
            }
        }
    }
}
