<?php

declare(strict_types=1);

namespace Params;

class ValidationResult
{
    /** @var string */
    private $value;

    /** @var string[] */
    private $problemMessages;

    /** @var bool */
    private $isFinalResult;

    /**
     * ValidationResult constructor.
     * @param mixed $value
     * @param string[] $problemMessages
     * @param bool $isFinalResult
     */
    private function __construct($value, array $problemMessages, bool $isFinalResult)
    {
        $this->value = $value;
        $this->problemMessages = $problemMessages;
        $this->isFinalResult = $isFinalResult;
    }

    /**
     * @param string $name
     * @param string $message
     * @return ValidationResult
     */
    public static function errorResult(string $name, string $message)
    {
        return new self(null, ['/' . $name => $message], true);
    }

    /**
     * @param string[] $messages
     * @return ValidationResult
     */
    public static function errorsResult(array $messages)
    {
        return new self(null, $messages, true);
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

    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string[]
     */
    public function getProblemMessages(): array
    {
        return $this->problemMessages;
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
