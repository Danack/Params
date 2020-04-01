<?php

namespace Params;

/**
 * @param mixed $value
 */
function getTypeForErrorMessage($value): string
{
    if (is_object($value) === true) {
        return get_class($value);
    }

    return gettype($value);
}
