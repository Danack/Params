<?php

declare(strict_types = 1);

namespace Params\InputStorage;

use Params\Exception\InvalidLocationException;
use VarMap\VarMap;
use function Params\getJsonPointerParts;

/**
 * Implementation of InputStorage that wraps around a simple array.
 */
class ArrayInputStorage implements InputStorage
{
    private array $data;

    private array $currentLocation = [];

    private function __construct(array $data)
    {
        $this->data = $data;
    }

    public static function fromArray(array $data): self
    {
        $instance = new self($data);

        return $instance;
    }

    /**
     * @param int|string $key
     * @param mixed $value
     * @return self
     */
    public static function fromSingleValue($key, $value): self
    {
        $data = [$key => $value];
        $instance = self::fromArray($data);

        return $instance->moveKey($key);
    }

    /**
     * @param int|string $key
     * @param mixed $value
     * @return self
     */
    public static function fromSingleValueButRoot($key, $value): self
    {
        $data = [$key => $value];
        $instance = self::fromArray($data);

        return $instance;
    }

    /**
     * Used for testing missing data mostly.
     * @param string $key
     * @return self
     */
    public static function createMissing(string $key): self
    {
        $instance = new self([]);

        return $instance->moveKey($key);
    }


    /**
     * @todo - remove usages of this
     */
    public static function fromArraySetFirstValue(array $data): self
    {
        $instance = new self($data);

        foreach ($data as $key => $value) {
            return $instance->moveKey($key);
        }

        return $instance;
    }

    public static function fromVarMap(VarMap $varMap): self
    {
        return self::fromArray($varMap->toArray());
    }

    public static function fromVarMapAndSetFirstValue(VarMap $varMap): self
    {
        return self::fromArraySetFirstValue($varMap->toArray());
    }

    /**
     * @return mixed
     */
    public function getCurrentValue()
    {
        $data = $this->data;

        foreach ($this->currentLocation as $key) {
            if (array_key_exists($key, $data) !== true) {
                // This would only happen if this was called
                // when the data had been move to a 'wrong' place.
                throw new InvalidLocationException();
            }

            $data = $data[$key];
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function isValueAvailable(): bool
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

    /**
     * @inheritDoc
     */
    public function moveKey($name): self
    {
        $clone = clone $this;
        $clone->currentLocation[] = $name;

        return $clone;
    }

    /**
     * @inheritDoc
     */
    public function getPath(): string
    {
        $path = '';

        if (count($this->currentLocation) === 0) {
            return '/';
        }

        $separator_needed = true;

        foreach ($this->currentLocation as $location) {
            if (is_int($location) === true) {
                if ($separator_needed === true) {
                    $path .= "/";
                }

                $path .= "[$location]";
            }
            if (is_string($location) === true) {
                $path .= '/';
                $path .= "$location";
            }

            $separator_needed = false;
        }

        return $path;
    }


    public function setLocationFromJsonPointer(string $jsonPointer): self
    {
        $parts = getJsonPointerParts($jsonPointer);
        $clone = clone $this;
        foreach ($parts as $part) {
            $clone->currentLocation[] = $part;
        }

        return $clone;
    }
}
