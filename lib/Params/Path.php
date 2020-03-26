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

    private function __construct()
    {
    }

    public static function initial()
    {
        return new self();
    }

    public static function fromName(string $name)
    {
        $instance = new self();
        $instance = $instance->addNamePathFragment($name);
        return $instance;
    }

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

    /**
     * @return string|int
     * @throws \Exception
     */
    public function getCurrentName()
    {
        $lastFragment = end($this->pathFragments);

        if ($lastFragment instanceof NameFragment) {
            return $lastFragment->getName();
        }

        if ($lastFragment instanceof ArrayIndexFragment) {
            return $lastFragment->getIndex();
        }

        throw new \Exception("Unknown fragment type [" . getType($lastFragment) . "].");
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
