<?php

declare(strict_types=1);

namespace ParamsExample;

use Respect\Validation\Validator as v;
use Params\Rule;
use Params\ValidationResult;
use Params\OpenApi\ParamDescription;

/**
 * This is an example of using a validator from the Respect/Validation library
 *
 *
 */
class RespectMacRule implements Rule
{
    public function __invoke(string $name, $value): ValidationResult
    {
        if (v::macAddress()->validate($value) === true) {
            return ValidationResult::valueResult($value);
        }

        $message = sprintf(
            "String [%s] is not a valid mac address.",
            substr($value, 0, 64)
        );

        return ValidationResult::errorResult($message);
    }

    public function updateParamDescription(ParamDescription $paramDescription)
    {
        throw new \Exception("updateParamDescription not implemented yet.");
    }
}
