<?php

declare(strict_types = 1);

namespace Params\DataLocator;

class SingleValueDataLocator implements DataLocator
{
    /** @var mixed */
    private $value;

    public static function create($value): self
    {
        $instance = new self();
        $instance->value = $value;

        return $instance;
    }

    public function getCurrentValue()
    {
        return $this->value;
    }

    public function valueAvailable(): bool
    {
        return true;
    }

    public function toString(): string
    {
        return $this->getPath();
    }

    public function getPath(): string
    {
        return '/';
//        throw new \Exception("getPath not implemented yet.");
    }

    public function moveIndex(int $index): DataLocator
    {
        throw new \Exception("moveIndex not implemented yet.");
//        return clone $this;
    }

    public function moveKey(string $name): DataLocator
    {
        throw new \Exception("moveKey not implemented yet.");
    }
}
