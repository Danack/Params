<?php

declare(strict_types = 1);

namespace Params\DataLocator;

interface InputStorageAye
{
    /** @return mixed */
    public function getCurrentValue();

    public function getPath(): string;

    /**
     * @param int|string $name
     * @return $this
     */
    public function moveKey($name): self;

    public function valueAvailable(): bool;
}
