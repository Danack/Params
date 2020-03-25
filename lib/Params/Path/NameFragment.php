<?php

declare(strict_types = 1);

namespace Params\Path;

class NameFragment implements Fragment
{
    private string $name;

    /**
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    public function toString(): string
    {
        return $this->name;
    }
}
