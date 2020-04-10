<?php

declare(strict_types = 1);

namespace Params\DataLocator;

use VarMap\VarMap;

class DataStorage implements InputStorageAye
{
    private array $data;

    private array $currentLocation = [];

    public static function fromArray(array $data)
    {
        $instance = new self();
        $instance->data = $data;

        return $instance;
    }

    public static function fromSingleValue($key, $value): self
    {
        $data = [$key => $value];
        $instance = self::fromArray($data);

        return $instance->moveKey($key);
    }

    public static function fromArraySetFirstValue(array $data)
    {
        $instance = new self();
        $instance->data = $data;

        foreach ($data as $key => $value) {
            return $instance->moveKey($key);
        }

        return $instance;
    }

    public static function fromVarMap(VarMap $varMap)
    {
        return self::fromArray($varMap->hackGetRawData());
    }

    public static function fromVarMapAndSetFirstValue(VarMap $varMap)
    {
        return self::fromArraySetFirstValue($varMap->hackGetRawData());
    }

    /**
     * @return mixed
     */
    public function getCurrentValue()
    {
        $data = $this->data;

        foreach ($this->currentLocation as $key) {
            // TODO - check not set...
            // TODO - we'll be yielding in the future.
            $data = $data[$key];
        }

        return $data;
    }

    public function valueAvailable(): bool
    {
        $data = $this->data;

        foreach ($this->currentLocation as $location) {
            if (is_array($data) === false) {
                return false;
            }

            if (array_key_exists($location, $data) === false) {
                return false;
            }

            $data = $data[$location];
        }

        return true;
    }

    public function moveIndex($index): self
    {
        $clone = clone $this;
        $clone->currentLocation[] = $index;

        return $clone;
    }

    /**
     * @param int|string $name
     * @return $this
     */
    public function moveKey($name): self
    {
        $clone = clone $this;
        $clone->currentLocation[] = $name;

        return $clone;
    }

    public function toString(): string
    {
        return $this->getPath();
    }

    public function getPath(): string
    {
        $path = '';

        if (count($this->currentLocation) === 0) {
            return '/';
        }

        foreach ($this->currentLocation as $location) {
            $path .= '/';

            if (is_int($location) === true) {
                $path .= "[$location]";
            }
            if (is_string($location) === true) {
                $path .= "$location";
            }
        }

        return $path;
    }
}
