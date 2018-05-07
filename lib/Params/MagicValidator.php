<?php

declare(strict_types=1);

namespace Params;

class MagicValidator
{
    public $generators = [];

    private $validationProblems = [];

    public function &addRule(string $name, array $rules)
    {
        $paramValidator = new MagicInternalValidator($name, $rules);
        $fn = $paramValidator->getGenerator($this->validationProblems);
        $this->generators[] = &$fn;

        return $fn;
    }

    public function validate()
    {
        for ($i=0; $i<count($this->generators); $i++) {
            $this->generators[$i] = $this->generators[$i]();
        }

        return $this->validationProblems;
    }
}

