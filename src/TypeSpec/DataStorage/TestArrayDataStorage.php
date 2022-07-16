<?php

declare(strict_types = 1);

namespace TypeSpec\DataStorage;

class TestArrayDataStorage extends ArrayDataStorage
{
    /**
     * DataStorage objects remember what position they are currently in.
     * This static method creates an ArrayDataStorage with a single value,
     * with the position setup to read that value back as the 'current' value.
     *
     * This factory method is for use in integration tests, where the code
     * is going through the functions that manage DataStorage position.
     *
     * @param int|string $key
     * @param mixed $value
     * @return DataStorage
     */
    public static function fromSingleValueAndSetCurrentPosition($key, $value): DataStorage
    {
        $data = [$key => $value];
        $instance = self::fromArray($data);

        return $instance->moveKey($key);
    }

    /**
     * DataStorage objects remember what position they are currently in.
     * This static method creates an ArrayDataStorage with a single value,
     * with the position setup as root. The current position will need to
     * be advanced by calling 'moveKey' to the $key value.
     *
     * This factory method is for use in integration tests, where the code
     * is going through the functions that manage DataStorage positon.
     *
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
     * @return DataStorage
     */
    public static function createEmptyAtRoot(): DataStorage
    {
        $instance = new self([]);

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
