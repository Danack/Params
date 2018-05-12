<?php

declare(strict_types=1);

namespace Params;

/**
 * Class MagicInternalValidator
 *
 * This is basically an experiment in trolling.
 */
class MagicInternalValidator
{
    /** @var \Params\Rule[] */
    private $rules;

    /** @var string */
    private $name;

    /**
     * @param \Params\Rule[] $rules
     */
    public function __construct(string $name, array $rules)
    {
        $this->name = $name;
        $this->rules = $rules;
    }

    public function getGenerator(&$validationProblems)
    {
        $fn = null;

        $fn = function () use (&$validationProblems) {
            $value = null;
            foreach ($this->rules as $rule) {
                $validationResult = $rule($this->name, $value);
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

            return $value;
        };

        return $fn;
    }
}
