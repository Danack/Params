<?php

declare(strict_types = 1);

namespace Params\Path;

interface Fragment
{
    public function toString(): string;
}
