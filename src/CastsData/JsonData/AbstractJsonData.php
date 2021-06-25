<?php

namespace FormEntries\CastsData\JsonData;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

abstract class AbstractJsonData implements \JsonSerializable
{
    protected Model $model;

    protected array $data;

    public function __construct(Model $model, array $data = [])
    {
        $this->data  = $data;
        $this->model = $model;
    }

    /**
     * @param array $data
     *
     * @return AbstractJsonData
     */
    public function setData(array $data = [])
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @param array $keys
     * @return array
     */
    public function getRawData(array $keys = []): array
    {
        if (!empty($keys)) {
            return Arr::only($this->data, array_flip($keys));
        }

        return $this->data;
    }

    /**
     * @param string $key
     * @param null $default
     *
     * @return array|\ArrayAccess|mixed
     */
    public function getAttribute(string $key, $default = null)
    {
        return Arr::get($this->data, $key, $default);
    }

    public function setAttribute(string $key, $value): AbstractJsonData
    {
        Arr::set($this->data, $key, $value);

        return $this;
    }

    public function removeAttribute(string $key)
    {
        Arr::forget($this->data, $key);

        return $this;
    }

    public function toArray(): array
    {
        return $this->data;
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * @param array $keys
     * @return array
     */
    public function getRawDataExcept(array $keys = []): array
    {
        if (!empty($keys)) {
            return array_diff_key($this->data, array_flip($keys));
        }

        return $this->data;
    }
}
