<?php

namespace FormEntries\CastsData;

use FormEntries\CastsData\JsonData\AbstractJsonData;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

abstract class AbstractMetaCastable implements CastsAttributes
{
    public function get($model, $key, $value, $attributes): AbstractJsonData
    {
        $data  = json_decode($value, true);
        $class = $this->metaClass($model);

        return new $class($model, is_array($data) ? $data : []);
    }

    public function set($model, $key, $value, $attributes): string
    {
        return json_encode($value);
    }

    abstract protected function metaClass($model): string;
}
