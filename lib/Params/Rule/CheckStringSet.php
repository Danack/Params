<?php

declare(strict_types=1);

namespace Params\Rule;

use Params\Rule;
use Params\ValidationResult;
use VarMap\VarMap;

//class CheckStringSet implements Rule
//{
//    /** @var VarMap */
//    private $variableMap;
//
//    public function __construct(VarMap $variableMap)
//    {
//        $this->variableMap = $variableMap;
//    }
//
//    /**
//     * @inheritdoc
//     */
//    public function __invoke(string $variableName, $_) : ValidationResult
//    {
//        if ($this->variableMap->has($variableName) !== true) {
//            return ValidationResult::errorResult('Value not set for ' . $variableName . '.');
//        }
//
//        $value = (string)$this->variableMap->get($variableName);
//
//        return ValidationResult::valueResult($value);
//    }
//}
