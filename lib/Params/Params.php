<?php

declare(strict_types = 1);

namespace Params;

use Params\Exception\ValidationException;
use Params\Exception\ParamsException;

class Params
{
    /**
     * @param array $namedRules
     * @return array
     * @throws ValidationException
     * @throws ParamsException
     */
    public static function validate($namedRules)
    {
        $values = [];
        $validationProblems = [];

        foreach ($namedRules as $name => $rules) {
            if (count($rules) === 0) {
                throw new ParamsException('Rules for validating ' . $name . ' are not set.');
            }

            $value = null;
            foreach ($rules as $rule) {
                $validationResult = $rule($name, $value);
                /** @var $validationResult \Params\ValidationResult */
                if (($validationProblem = $validationResult->getProblemMessage()) != null) {
                    $validationProblems[] = $validationProblem;
                    break;
                }
                if ($validationResult->isFinalResult() === true) {
                    break;
                }
                $value = $validationResult->getValue();
            }
            $values[] = $value;
        }

        ValidationException::throwIfProblems("Validation problems", $validationProblems);

        return $values;
    }
}
