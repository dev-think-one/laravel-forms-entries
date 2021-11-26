<?php

namespace FormEntries\Models;

use FormEntries\Database\Factories\FormEntryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property \FormEntries\Forms\FormContent      $content
 * @property \JsonFieldCast\Json\SimpleJsonField $meta
 */
class FormEntry extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'notified_at' => 'datetime',
        'content'     => \FormEntries\Casts\FormContentCast::class,
        'meta'        => \JsonFieldCast\Casts\SimpleJsonField::class,
    ];

    protected static function newFactory(): FormEntryFactory
    {
        return new FormEntryFactory();
    }

    /**
     * @inheritDoc
     */
    public function getTable()
    {
        return $this->table ?? config('forms-entries.tables.forms-entries', parent::getTable());
    }

    public function sender()
    {
        return $this->morphTo('sender');
    }
}
