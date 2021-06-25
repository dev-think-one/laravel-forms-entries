<?php


namespace FormEntries\CastsData;

use FormEntries\CastsData\JsonData\FormEntryMetaJson;

class FormEntryMetaCastable extends AbstractMetaCastable
{
    protected function metaClass($model): string
    {
        return FormEntryMetaJson::class;
    }
}
