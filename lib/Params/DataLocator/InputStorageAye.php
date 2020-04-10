<?php

declare(strict_types = 1);

namespace Params\DataLocator;

interface InputStorageAye
{
    /** @return mixed */
    public function getCurrentValue();

    public function getPath(): string;

    public function moveIndex(int $index): self;

    /**
     * @param int|string $name
     * @return $this
     */
    public function moveKey($name): self;

    public function valueAvailable(): bool;

    // todo - need a get by absolute path
//    public function getResultByRelativeKey($relativeKey);
}
