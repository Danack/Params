<?php

declare(strict_types=1);

namespace Type;

use Type\OpenApi\ParamDescription;

/**
 * All rules must be able to update the ParamDescription, so that an
 * OpenAPI description can be generated for all of the parameters.
 */
interface PropertyRule
{
    public function updateParamDescription(ParamDescription $paramDescription): void;
}
