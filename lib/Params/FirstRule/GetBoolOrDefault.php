<?php

declare(strict_types=1);

namespace Params\FirstRule;

use Params\SubsequentRule\BoolInput;
use Params\ValidationResult;
use VarMap\VarMap;
use Params\OpenApi\ParamDescription;
use Params\ParamsValidator;
use Params\ParamValues;

class GetBoolOrDefault implements FirstRule
{
    /** @var bool */
    private $default;

    /**
     * setOrDefaultValidator constructor.
     * @param bool $default The default value to use.
     */
    public function __construct(bool $default)
    {
        $this->default = $default;
    }

    public function process(
        string $name,
        VarMap $varMap,
        ParamValues $validator
    ): ValidationResult {
        if ($varMap->has($name) === true) {
            $value = $varMap->get($name);
        }
        else {
            return ValidationResult::valueResult($this->default);
        }

        $intRule = new BoolInput();
        return $intRule->process($name, $value, $validator);
    }

    public function updateParamDescription(ParamDescription $paramDescription)
    {
        $paramDescription->setType(ParamDescription::TYPE_BOOLEAN);
        $paramDescription->setDefault($this->default);
        $paramDescription->setRequired(false);
    }
}
