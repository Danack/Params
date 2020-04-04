<?php

declare(strict_types = 1);

namespace Params\DataLocator;

interface DataLocator
{
    /** @return mixed */
    public function getCurrentValue();

    public function getPath(): string;

    public function moveIndex(int $index): self;

    public function moveKey(string $name): self;

    public function valueAvailable(): bool;
}