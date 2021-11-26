<?php

namespace FormEntries\Forms;

trait HasTypesMap
{
    /**
     * An array to map class names to their slug names.
     *
     * @var array
     */
    public static array $typesMap = [];

    public static function typesMap(array $map = null, $merge = true): array
    {
        if (is_array($map)) {
            static::$typesMap = $merge && static::$typesMap
                ? array_merge(static::$typesMap, $map) : $map;
        }

        return static::$typesMap;
    }

    public function getType(): string
    {
        $typesMap = static::typesMap();

        if (!empty($typesMap) && in_array(static::class, $typesMap)) {
            return array_search(static::class, $typesMap, true);
        }

        return static::class;
    }

    public static function getClassByType(?string $type = null): ?string
    {
        $slugsMap = static::typesMap();

        if (!empty($slugsMap[$type])) {
            return $slugsMap[$type];
        }

        return class_exists($type) ? $type : null;
    }
}
