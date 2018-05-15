<?php

declare(strict_types=1);

namespace Params;

class ParamRules
{
    /** @var string */
    private $inputName;

    /** @var \Params\Rule */
    private $rules;

    /**
     * ParamRules constructor.
     * @param string $inputName
     * @param Rule $rules
     */
    public function __construct(string $inputName, Rule $rules)
    {
        $this->inputName = $inputName;
        $this->rules = $rules;
    }
}
