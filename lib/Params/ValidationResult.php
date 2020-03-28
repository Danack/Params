<?php

declare(strict_types=1);

namespace Params;

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
     *
     */
    public static function errorResult(Path $path, string $message): ValidationResult
    {
        return new self(
            null,
            [new ValidationProblem($path, $message)],
            true
        );
    }

    /**
     * @param \Params\ValidationProblem[] $validationProblems
     * @return ValidationResult
     */
    public static function thisIsMultipleErrorResult(array $validationProblems)
    {
        foreach ($validationProblems as $key => $validationProblem) {
            if (is_int($key)  === false) {
                throw new \LogicException("Key for array must be integer");
            }
            if (!($validationProblem instanceof ValidationProblem)) {
                throw new \LogicException(
                    "Array must contain only 'ValidationProblem's instead got " . gettype($validationProblem)
                );
            }
        }

        // TODO - check that arrays are not string indexed, as this
        // breaks the combining code.

        return new self(null, $validationProblems, true);
    }

    /**
     * @param mixed $value
     * @return ValidationResult
     */
    public static function valueResult($value)
    {
        return new self($value, [], false);
    }

    /**
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
