<?php

declare(strict_types = 1);

namespace Params;

use Params\Path\ArrayIndexFragment;
use Params\Path\NameFragment;

class Path
{
    /**
     * @var \Params\Path\Fragment[]
     */
    private array $pathFragments = [];

    public function addNamePathFragment(string $name): self
    {
        $newInstance = clone $this;
        $newInstance->pathFragments[] = new NameFragment($name);
        return $newInstance;
    }

    public function addArrayIndexPathFragment(int $index): self
    {
        $newInstance = clone $this;
        $newInstance->pathFragments[] = new ArrayIndexFragment($index);
        return $newInstance;
    }

    public function toString(): string
    {
        $string = '';
        $separator = '';

        foreach ($this->pathFragments as $pathFragment) {
            if ($pathFragment instanceof NameFragment) {
                $string .= $separator;
            }

            $string .= $pathFragment->toString();
            $separator = '/';
        }

        return $string;
    }
}
