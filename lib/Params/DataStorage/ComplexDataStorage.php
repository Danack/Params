<?php

declare(strict_types = 1);

namespace Params\DataStorage;

use Params\Exception\InvalidLocationException;
use function Params\getJsonPointerParts;

/**
 * Implementation of InputStorage that wraps around data structures that
 * are composed of arrays, objects or scalar values.
 */
class ComplexDataStorage implements DataStorage
{
    private array|object $dto;

    /** @var array<string|int> */
    private array $currentLocation = [];

    protected function __construct(array|object $data)
    {
        $this->dto = $data;
    }

    public static function fromData(array|object $data): DataStorage
    {
        $instance = new self($data);

        return $instance;
    }


    /**
     * @return mixed
     */
    public function getCurrentValue(): mixed
    {
        $dto = $this->dto;

        foreach ($this->currentLocation as $key) {
            if (is_object($dto) === true) {
                if (is_int($key) === true) {
                    throw InvalidLocationException::intNotAllowedComplexDataStorage($this->currentLocation);
                }
                if (property_exists($dto, $key) === false) {
                    // This would only happen if this was called
                    // when the data had been move to a 'wrong' place.
                    throw InvalidLocationException::badComplexDataStorage($this->currentLocation);
                }
                /** @phpstan-ignore-next-line
                 * @psalm-suppress TypeDoesNotContainType
                 */
                $dto = $dto->{$key};
            }
            else if (is_array($dto) === true) {
                if (array_key_exists($key, $dto) !== true) {
                    // This would only happen if this was called
                    // when the data had been move to a 'wrong' place.
                    throw InvalidLocationException::badComplexDataStorage($this->currentLocation);
                }

                $dto = $dto[$key];
            }
            else {
                throw InvalidLocationException::badComplexDataStorage($this->currentLocation);
            }
        }

        return $dto;
    }

    /**
     * @inheritDoc
     */
    public function isValueAvailable(): bool
    {
        // TODO - this has a lot of code in common with getCurrentValue
        $dto = $this->dto;
        foreach ($this->currentLocation as $location) {
            if (is_object($dto) === true) {
                if (is_int($location) === true) {
                    throw InvalidLocationException::intNotAllowedComplexDataStorage($this->currentLocation);
                }
                if (property_exists($dto, $location) === false) {
                    return false;
                }

                /** @phpstan-ignore-next-line
                 * @psalm-suppress TypeDoesNotContainType
                 */
                $dto = $dto->{$location};
            }
            else if (is_array($dto) === true) {
                if (array_key_exists($location, $dto) === false) {
                    return false;
                }

                $dto = $dto[$location];
            }
            else {
                throw InvalidLocationException::badComplexDataStorage($this->currentLocation);
            }
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
