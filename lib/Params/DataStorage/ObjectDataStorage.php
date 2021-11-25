<?php

declare(strict_types = 1);

namespace Params\DataStorage;

use Params\Exception\InvalidLocationException;
use function Params\getJsonPointerParts;

/**
 * Implementation of InputStorage that wraps around a simple array.
 */
class ObjectDataStorage implements DataStorage
{
    private object $dto;

    private array $currentLocation = [];

    protected function __construct(object $data)
    {
        $this->dto = $data;
    }

    public static function fromObject(object $data): DataStorage
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
            if (property_exists($dto, $key) === false) {
                // This would only happen if this was called
                // when the data had been move to a 'wrong' place.
                throw new InvalidLocationException();
            }
            /** @phpstan-ignore-next-line
             *  @psalm-suppress TypeDoesNotContainType
             */
            $dto = $dto->{$key};
        }

        return $dto;
    }

    /**
     * @inheritDoc
     */
    public function isValueAvailable(): bool
    {
        $dto = $this->dto;
        foreach ($this->currentLocation as $location) {
            if (property_exists($dto, $location) === false) {
                return false;
            }

            /** @phpstan-ignore-next-line
             *  @psalm-suppress TypeDoesNotContainType
             */
            $dto = $dto->{$location};
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function moveKey($name): DataStorage
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
