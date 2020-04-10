<?php

declare(strict_types = 1);

namespace Params\DataLocator;

class EmptyInputStorageAye implements InputStorageAye
{
    private array $data;

    private array $currentLocation = [];

    /**
     * @param mixed $pathPieces
     * @return self
     */
    public static function fromPath($pathPieces): self
    {
        $instance = new self();
        $instance->currentLocation = $pathPieces;

        return $instance;
    }

    public function getCurrentValue()
    {
        throw new \Exception("getCurrentValue not implemented yet.");
    }

    public function toString(): string
    {
        return $this->getPath();
    }

    public function getPath(): string
    {
        $path = '';

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

    public function moveIndex(int $index): InputStorageAye
    {
        throw new \Exception("moveIndex not implemented yet.");
    }

    public function moveKey($name): InputStorageAye
    {
        throw new \Exception("moveKey not implemented yet.");
    }

    public function valueAvailable(): bool
    {
        return false;
    }
}
