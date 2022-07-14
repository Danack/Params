<?php

declare(strict_types = 1);

namespace TypeSpec\DataStorage;

class TestArrayDataStorage extends ArrayDataStorage
{
    /**
     * @param int|string $key
     * @param mixed $value
     * @return DataStorage
     */
    public static function fromSingleValue($key, $value): DataStorage
    {
        $data = [$key => $value];
        $instance = self::fromArray($data);

        return $instance->moveKey($key);
    }

    /**
     * @param int|string $key
     * @param mixed $value
     * @return DataStorage
     */
    public static function fromSingleValueButRoot($key, $value): DataStorage
    {
        $data = [$key => $value];
        $instance = self::fromArray($data);

        return $instance;
    }


    /**
     * Used for testing missing data mostly.
     * @param string $key
     * @return DataStorage
     */
    public static function createMissing(string $key): DataStorage
    {
        $instance = new self([]);

        return $instance->moveKey($key);
    }

    /**
     * @todo - is this needed?
     */
    public static function fromArraySetFirstValue(array $data): DataStorage
    {
        $instance = new self($data);

        foreach ($data as $key => $value) {
            return $instance->moveKey($key);
        }

        return $instance;
    }
}
