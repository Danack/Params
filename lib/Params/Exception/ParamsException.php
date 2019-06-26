<?php

declare(strict_types=1);

namespace Params\Exception;

/**
 * Root class for all exceptions for this library
 */
class ParamsException extends \Exception
{
    public const ERROR_FIRST_RULE_MUST_IMPLEMENT_FIRST_RULE = "First rule must implement the FirstRule interfaces";

    public static function badFirstRule()
    {
        return new self(self::ERROR_FIRST_RULE_MUST_IMPLEMENT_FIRST_RULE);
    }
}
