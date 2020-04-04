<?php

declare(strict_types = 1);

namespace Params\DataLocator;

class NotAvailableDataLocator implements DataLocator
{
    public function getCurrentValue()
    {
        throw new \Exception("Values is not available");
    }

    public function getPath(): string
    {
        throw new \Exception("getPath not implemented yet.");
    }

    public function moveIndex(int $index): DataLocator
    {
        return $this;
    }

    public function moveKey(string $name): DataLocator
    {
        return $this;
    }

    public function valueAvailable(): bool
    {
        return false;
    }
}
