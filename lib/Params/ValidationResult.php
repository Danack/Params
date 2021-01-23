<?php

declare(strict_types=1);

namespace Params;

use Params\InputStorage\InputStorage;
use Params\Exception\LogicException;

/**
 *
 */
class ValidationResult
{
    /** @var mixed */
    private $value;

    /** @var \Params\ValidationProblem[] */
    private array $validationProblems;

    private bool $isFinalResult;

    /**
     * ValidationResult constructor.
     * @param mixed $value
     * @param \Params\ValidationProblem[] $problemMessages
     * @param bool $isFinalResult
     */
    private function __construct($value, array $problemMessages, bool $isFinalResult)
    {
        $this->value = $value;
        $this->validationProblems = $problemMessages;
        $this->isFinalResult = $isFinalResult;
    }


    /**
     * this is for a single value processing.
     * @param InputStorage $inputStorage
     * @param string $message
     * @return ValidationResult
     */
    public static function errorResult(
        InputStorage $inputStorage,
        string $message
    ): ValidationResult {
        return new self(
            null,
            [new ValidationProblem($inputStorage, $message)],
            true
        );
    }


    /**
     * @param \Params\ValidationProblem[] $validationProblems
     * @return ValidationResult
     */
    public static function fromValidationProblems(array $validationProblems): self
    {
        foreach ($validationProblems as $key => $validationProblem) {
            if (is_int($key)  === false) {
                throw LogicException::keysMustBeIntegers();
            }
            if (!($validationProblem instanceof ValidationProblem)) {
                throw LogicException::onlyInputParameters(
                    $validationProblem
                );
            }
        }

        // TODO - check that arrays are not string indexed, as this
        // breaks the combining code.

        return new self(null, $validationProblems, true);
    }

    /**
     * Create a ValidationResult where the value is valid and there are no errors.
     * It is not a 'final' result and any subsequent ProcessRules should be processed.
     * @param mixed $value
     * @return ValidationResult
     */
    public static function valueResult($value): self
    {
        return new self($value, [], false);
    }

    /**
     * Create a ValidationResult where the value is valid and there are no errors.
     * It is a 'final' result and any subsequent ProcessRules should be skipped.
     *
     * @param mixed $value
     * @return ValidationResult
     */
    public static function finalValueResult($value)
    {
        return new self($value, [], true);
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return \Params\ValidationProblem[]
     */
    public function getValidationProblems(): array
    {
        return $this->validationProblems;
    }

    /**
     * Whether any errors were found.
     */
    public function anyErrorsFound(): bool
    {
        if (count($this->validationProblems) !== 0) {
            return true;
        }
        return false;
    }

    /**
     * Return true if there should not be any more processing of the
     * rules for this parameter. e.g. both errors and null results stop
     * the processing.
     *
     * @return bool
     */
    public function isFinalResult(): bool
    {
        return $this->isFinalResult;
    }
}
