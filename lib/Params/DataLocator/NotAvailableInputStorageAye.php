<?php

declare(strict_types = 1);

namespace Params\DataLocator;

class NotAvailableInputStorageAye implements InputStorageAye
{
    public function getCurrentValue()
    {
        throw new \Exception("Values is not available");
    }

    public function getPath(): string
    {
        throw new \Exception("getPath not implemented yet.");
    }

    public function moveIndex(int $index): InputStorageAye
    {
        return $this;
    }

    public function moveKey($name): InputStorageAye
    {
        return $this;
    }

    public function valueAvailable(): bool
    {
        return false;
    }
}
