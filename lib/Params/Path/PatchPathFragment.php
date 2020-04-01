<?php

declare(strict_types = 1);

namespace Params\Path;

class PatchPathFragment implements Fragment
{
    public function toString(): string
    {
        return '/';
    }
}
