<?php

declare(strict_types = 1);

namespace Params;

interface ParamValues
{
    public function hasParam(string $name);

    public function getParam(string $name);
}
