<?php

declare(strict_types=1);

namespace ParamsExample;

use Respect\Validation\Validator as v;
use Params\SubsequentRule\SubsequentRule;
use Params\ValidationResult;
use Params\OpenApi\ParamDescription;
use Params\ParamsValidator;
use Params\ParamValues;

/**
 * This is an example of using a validator from
 * the Respect/Validation library
 */
class RespectMacRule implements SubsequentRule
{
    public function process(string $name, $value, ParamValues $validator) : ValidationResult
    {
        if (v::macAddress()->validate($value) === true) {
            return ValidationResult::valueResult($value);
        }

        $message = sprintf(
            "String [%s] is not a valid mac address.",
            substr($value, 0, 64)
        );

        return ValidationResult::errorResult($name, $message);
    }

    public function updateParamDescription(ParamDescription $paramDescription)
    {
        throw new \Exception("updateParamDescription not implemented yet.");
    }
}
