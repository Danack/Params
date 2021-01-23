<?php

declare(strict_types=1);

namespace Params;

use Params\OpenApi\ParamDescription;

/**
 * All rules must be able to update the ParamDescription, so that an
 * OpenAPI description can be generated for all of the parameters.
 */
interface Rule
{
    public function updateParamDescription(ParamDescription $paramDescription): void;
}
