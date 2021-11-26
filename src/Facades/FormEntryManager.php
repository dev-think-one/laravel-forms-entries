<?php

namespace FormEntries\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \FormEntries\FormEntryManager routes()
 */
class FormEntryManager extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \FormEntries\FormEntryManager::class;
    }
}
