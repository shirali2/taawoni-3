<?php

namespace App\Traits;

use App\Scopes\CollectionManagerScope;

trait HasCollectionScope
{
    /**
     * بوت کردن تریت و افزودن Global Scope.
     */
    public static function bootHasCollectionScope()
    {
        static::addGlobalScope(new CollectionManagerScope);
    }
}
