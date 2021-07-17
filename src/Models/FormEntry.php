<?php

namespace FormEntries\Models;

use FormEntries\CastsData\FormEntryContentCastable;
use FormEntries\CastsData\FormEntryMetaCastable;
use Illuminate\Database\Eloquent\Model;

class FormEntry extends Model
{
    protected $guarded = [];


    protected $casts = [
        'content' => FormEntryContentCastable::class,
        'meta'    => FormEntryMetaCastable::class,
    ];

    /**
     * @inheritDoc
     */
    public function getTable()
    {
        return config('forms-entries.tables.forms-entries', parent::getTable());
    }

    public function sender()
    {
        return $this->morphTo('sender');
    }
}
