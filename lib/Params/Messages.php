<?php

declare(strict_types = 1);

namespace Params;

class Messages
{
    // Array
    public const ERROR_MESSAGE_NOT_ARRAY = "Value must be an array.";
    public const ERROR_MESSAGE_NOT_ARRAY_VARIANT_1 = "Value must be an array.";
    public const BAD_TYPE_FOR_ARRAY_ACCESS = "Cannot use type [%s] for array access";
    public const ERROR_TOO_FEW_ELEMENTS = "Number of elements too small. Min allowed is %d but only got %d.";

    public const ERROR_TOO_MANY_ELEMENTS = "Number of elements too large. Max allowed is %d but got %d.";

    public const ERROR_WRONG_TYPE = "Minimum count can only be applied to an array but tried to operate on %s.";


    public const ERROR_WRONG_TYPE_VARIANT_1 = "Maximum count can only be applied to an array but tried to operate on %s.";

    // Bool

    // DateTime
    public const ERROR_INVALID_DATETIME = 'Value was not a valid RFC3339 date time apparently';

    // Enum
    // TODO - this message looks wrong.
    public const ENUM_MAP_UNRECOGNISED_VALUE_MULTIPLE = "Value [%s] is not known. Please use any of %s.";
    public const ENUM_MAP_UNRECOGNISED_VALUE_SINGLE = "Value is not known. Please use one of %s";

    // General errors
    public const VALUE_NOT_SET = 'Value not set.';
    public const ERROR_MESSAGE_NOT_SET = "Value not set.";
    public const ERROR_MESSAGE_NOT_SET_VARIANT_1 = "Value must be set.";
    public const INVALID_JSON_POINTER_FIRST = "First character must be /";

    // When a input type can't be converted to a desired type
    // e.g. resource -> bool
    public const UNSUPPORTED_TYPE = "Unsupported input type of '%s'";

    // Int

    public const INT_REQUIRED_FOUND_NON_DIGITS = "Must contain only digits. Non-digit found at position %d.";
    public const INT_REQUIRED_FOUND_NON_DIGITS2 = "Value must contain only digits.";
    public const INTEGER_TOO_LONG = "Value too long, max %s digits";
    public const INT_TOO_SMALL = "Value too small. Min allowed is %s";
    public const INT_TOO_LARGE = "Value too large. Max allowed is %s";
    public const INT_REQUIRED_UNSUPPORTED_TYPE = "Value must be int, found '%s'";
    public const INT_REQUIRED_FOUND_EMPTY_STRING = "Value is an empty string - must be an integer.";
    public const INT_OVER_LIMIT = "Value too large. Max allowed is %s";

    // Float
    public const NEED_FLOAT_NOT_EMPTY_STRING = "Value is an empty string - must be a floating point number.";

    public const FLOAT_REQUIRED = "Value must be a floating point number.";
    public const FLOAT_REQUIRED_WRONG_TYPE = "Value must be a floating point number but found '%s'";
    public const FLOAT_REQUIRED_FOUND_WHITESPACE = "Value must be floating point number, whitespace found.";

    // Order
    public const ORDER_VALUE_UNKNOWN = "Cannot order by [%s], as not known for this operation. Known are [%s]";


    // String
    public const STRING_TOO_SHORT = "String too short, min characters is %d";
    public const STRING_TOO_LONG = "String too long, max characters is %d.";
    public const STRING_INVALID_COMBINING_CHARACTERS = "Invalid combining characters found at position %s";
    public const STRING_REQUIRED_FOUND_NON_SCALAR = "String expected but found non-scalar.";
    public const STRING_REQUIRES_PREFIX = "The string must start with [%s].";
    public const STRING_FOUND_INVALID_CHAR = "Invalid character at position %d. Allowed characters are %s";

    // Types
    // \Params\InputParameterList::getInputParameterList
    public const MUST_RETURN_ARRAY_OF__INPUT_PARAMETER ='Function %s::getInputParameterList must return array of %s. Item at index %d is wrong type.';

    public const CLASS_NOT_FOUND = "Class %s isn't available through auto-loader.";
    public const CLASS_MUST_IMPLEMENT_INPUT_PARAMETER = "Class %s doesn't implement the %s interface. Cannot be used to get array of type.";


    // Rule erors
    public const ERROR_DIFFERENT_TYPES = "Parameter cannot by the same as %s as they different types, %s and %s.";

    public const ERROR_NO_PREVIOUS_PARAM = "Param named %s was not previously processed.";
    public const ERROR_DIFFERENT_VALUE = "Parameter is different to parameter '%s'.";

    public const ERROR_MAXIMUM_COUNT_MINIMUM = "Maximum count must be zero or above.";
    public const ERROR_MINIMUM_COUNT_MINIMUM = "Minimum count must be zero or above.";
    public const MUST_DUPLICATE_PARAMETER = "Must be duplicate of %s";
    public const NULL_NOT_ALLOWED = "null is not allowed.";
}
