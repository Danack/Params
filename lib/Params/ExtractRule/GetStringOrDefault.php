<?php

declare(strict_types=1);

namespace Params\ExtractRule;

use Params\ValidationResult;
use VarMap\VarMap;
use Params\OpenApi\ParamDescription;
use Params\ParamValues;
use Params\Path;

class GetStringOrDefault implements ExtractRule
{
    private ?string $default;

    /**
     * setOrDefaultValidator constructor.
     * @param ?string $default
     */
    public function __construct(?string $default)
    {
        $this->default = $default;
    }

    public function process(
        Path $path,
        VarMap $varMap,
        ParamValues $paramValues
    ): ValidationResult {
        if ($varMap->has($path->toString()) !== true) {
            return ValidationResult::valueResult($this->default);
        }

        $value = (string)$varMap->get($path->toString());

        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setType(ParamDescription::TYPE_STRING);
        $paramDescription->setDefault($this->default);
        $paramDescription->setRequired(false);
    }
}
