<?php

declare(strict_types = 1);

namespace Params\DataStorage;

use Params\Exception\InvalidLocationException;
use function Params\getJsonPointerParts;

/**
 * Implementation of InputStorage that wraps around a simple array.
 *
 * All entries in the hierarchy of data must be either an array or a scalar value.
 * i.e. no objects in it.
 *
 */
class ArrayDataStorage implements DataStorage
{
    private array $data;

    /**
     * @var array<string|int>
     */
    private array $currentLocation = [];

    protected function __construct(array $data)
    {
        $this->data = $data;
    }

    public static function fromArray(array $data): DataStorage
    {
        $instance = new self($data);

        return $instance;
    }


    /**
     * @return mixed
     */
    public function getCurrentValue(): mixed
    {
        $data = $this->data;

        foreach ($this->currentLocation as $key) {
            if (array_key_exists($key, $data) !== true) {
                // This would only happen if this was called
                // when the data had been move to a 'wrong' place.
                throw InvalidLocationException::badArrayDataStorage($this->currentLocation);
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
    public function moveKey(string|int $name): DataStorage
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
