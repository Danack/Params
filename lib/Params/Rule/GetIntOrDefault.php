<?php

declare(strict_types=1);

namespace Params\Rule;

use Params\Rule;
use Params\ValidationResult;
use VarMap\VarMap;

class GetIntOrDefault implements Rule
{
    private $default;

    /** @var VarMap */
    private $variableMap;

    /**
     * setOrDefaultValidator constructor.
     * @param mixed $default
     */
    public function __construct($default, VarMap $variableMap)
    {
        $this->default = $default;
        $this->variableMap = $variableMap;
    }

    /**
     * @inheritdoc
     */
    public function __invoke(string $name, $_) : ValidationResult
    {
        if ($this->variableMap->has($name) === true) {
            $value = $this->variableMap->get($name);
        }
        else {
            $value = $this->default;
        }

        $intRule = new IntegerInput();
        return $intRule($name, $value);
    }
}
