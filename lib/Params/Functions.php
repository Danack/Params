<?php

declare(strict_types=1);

namespace Params;

use Params\Exception\LogicException;
use Params\Value\Ordering;

class Functions
{
    /**
     * Separates an order parameter such as "+name", into the 'name' and
     * 'ordering' parts.
     * @param string $part
     * @return array
     */
    public static function normalise_order_parameter(string $part)
    {
        if (substr($part, 0, 1) === "+") {
            return [substr($part, 1), Ordering::ASC];
        }

        if (substr($part, 0, 1) === "-") {
            return [substr($part, 1), Ordering::DESC];
        }

        return [$part, Ordering::ASC];
    }

    /**
     * @param string $name string The name of the variable
     * @param mixed $value  The value of the variable
     * @return null|string returns an error string, when there is an error
     */
    public static function check_only_digits(string $name, $value)
    {
        if (is_int($value) === true) {
            return null;
        }

        $count = preg_match("/[^0-9]+/", $value, $matches, PREG_OFFSET_CAPTURE);

        if ($count === false) {
            throw new LogicException("preg_match failed");
        }

        if ($count !== 0) {
            $badCharPosition = $matches[0][1];
            $message = sprintf(
                "Value for '$name' must contain only digits. Non-digit found at position %d.",
                $badCharPosition
            );
            return $message;
        }

        return null;
    }

    public static function array_value_exists(array $array, $value)
    {
        return in_array($value, $array, true);
    }

    /**
     * Unescapes a json pointer part
     *
     * https://tools.ietf.org/html/rfc6901#section-4
     *
     * @param string $pointer
     */
    public static function escapeJsonPointer(string $pointer)
    {
        // then transforming any occurrence of the sequence '~0' to '~'
        $result = str_replace('~', '~0', $pointer);
        // first transforming any occurrence of the sequence '~1' to '/'
        $result = str_replace('/', '~1', $result);

        return $result;
    }


    /**
     * Unescapes a json pointer part
     *
     * https://tools.ietf.org/html/rfc6901#section-4
     *
     * @param string $pointer
     */
    public static function unescapeJsonPointer(string $pointer)
    {
        // first transforming any occurrence of the sequence '~1' to '/'
        $result = str_replace('~1', '/', $pointer);

        // then transforming any occurrence of the sequence '~0' to '~'

        $result = str_replace('~0', '~', $result);

        return $result;
    }



    public static function addChildErrorMessagesForArray(
        string $name,
        array $problems,
        array $errorsMessages
    ) {
        foreach ($problems as $key => $value) {
            $errorsMessages['/' . $name . $key] = $value;
        }

        return $errorsMessages;
    }
}
