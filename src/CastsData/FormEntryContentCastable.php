<?php


namespace FormEntries\CastsData;

use FormEntries\CastsData\JsonData\FormEntryContentJson;

class FormEntryContentCastable extends AbstractMetaCastable
{
    protected function metaClass($model): string
    {
        return $model->class_name ?? FormEntryContentJson::class;
    }
}
