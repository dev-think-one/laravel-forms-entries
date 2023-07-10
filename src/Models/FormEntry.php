<?php

namespace FormEntries\Models;

use FormEntries\Database\Factories\FormEntryFactory;
use Illuminate\Database\Eloquent\Builder;
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
    public function getTable(): string
    {
        return $this->table ?? config('forms-entries.tables.forms-entries', parent::getTable());
    }

    public function sender()
    {
        return $this->morphTo('sender');
    }

    public function requestIp(): string
    {
        $value = $this->meta->getAttribute('request_data.ip', '');

        return !is_string($value) ? '' : $value;
    }

    public function requestIps(): array
    {
        $ips = $this->meta->getAttribute('request_data.ips', []);

        return !is_array($ips) ? [] : $ips;
    }

    public function requestUserAgent(): string
    {
        $value = $this->meta->getAttribute('request_data.userAgent', '');

        return !is_string($value) ? '' : $value;
    }

    public function getNameAttribute(): string
    {
        return $this->content->formName();
    }

    public function scopeContent(Builder $query, string $contentType)
    {
        $query->where('content_type', '=', $contentType);
    }
}
