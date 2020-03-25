<?php

declare(strict_types = 1);

namespace Params\Path;

class ArrayIndexFragment implements Fragment
{
    private int $index;

    /**
     *
     * @param int $index
     */
    public function __construct(int $index)
    {
        $this->index = $index;
    }

    /**
     * @return int
     */
    public function getIndex(): int
    {
        return $this->index;
    }

    public function toString(): string
    {
        return '[' .$this->index . ']';
    }
}
