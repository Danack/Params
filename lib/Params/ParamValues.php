<?php

declare(strict_types = 1);

namespace Params;

/**
 * Holds variables that have been previously processed by the validator.
 * This allows later rules to reference earlier rules. This is useful for things
 * like a
 */
interface ParamValues
{
    public function hasParam(string $name);

    public function getParam(string $name);
}
