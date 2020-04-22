<?php

declare(strict_types = 1);

namespace Params;

class Messages
{
    public const ERROR_DIFFERENT_TYPES = "Parameter cannot by the same as %s as they different types, %s and %s.";

    public const ERROR_DIFFERENT_VALUE = "Parameter is different to parameter '%s'.";

    public const ERROR_INVALID_DATETIME = 'Value was not a valid RFC3339 date time apparently';

    public const ERROR_MAXIMUM_COUNT_MINIMUM = "Maximum count must be zero or above.";

    public const VALUE_NOT_SET = 'Value not set.';

    public const ERROR_MESSAGE_ITEM_NOT_ARRAY = "Values for type '%s' must be an array, but got '%s'. Use GetArrayOfInt|String for single values.";

    public const ERROR_MESSAGE_NOT_ARRAY = "Value must be an array.";

    public const ERROR_MESSAGE_NOT_ARRAY_VARIANT_1 = "Value must be an array.";

    public const ERROR_MESSAGE_NOT_SET = "Value not set.";

    public const ERROR_MESSAGE_NOT_SET_VARIANT_1 = "Value must be set.";

    public const ERROR_MINIMUM_COUNT_MINIMUM = "Minimum count must be zero or above.";

    public const ERROR_NO_PREVIOUS_PARAM = "Param named %s was not previously processed.";

    public const ERROR_TOO_FEW_ELEMENTS = "Number of elements too small. Min allowed is %d but only got %d.";

    public const ERROR_TOO_MANY_ELEMENTS = "Number of elements too large. Max allowed is %d but got %d.";

    public const ERROR_WRONG_TYPE = "Minimum count can only be applied to an array but tried to operate on %s.";

    public const ERROR_WRONG_TYPE_VARIANT_1 = "Maximum count can only be applied to an array but tried to operate on %s.";

    public const INVALID_CHAR_MESSAGE = "Invalid character at position %d. Allowed characters are %s";

    // TODO - this message looks wrong.
    public const MULTIPLE_ENUM_INVALID = "Cannot filter by [%s], as not known for this operation. Known are [%s]";


    public const INTEGER_TOO_LONG = "Value too long, max %s digits";

    public const STRING_TOO_LONG = "String too long, max characters is %d.";

    public const NULL_NOT_ALLOWED = "null is not allowed.";

    public const UNKNOWN_ORDERING = "Cannot order by [%s], as not known for this operation. Known are [%s]";

    public const ONLY_DIGITS_ALLOWED = "Must contain only digits. Non-digit found at position %d.";

    public const ONLY_DIGITS_ALLOWED_2 = "Value must contain only digits.";

    public const STRING_MUST_START_WITH = "The string must start with [%s].";

    // TODO - remove, I don't like this text.
    public const VALUE_MUST_BE_SCALAR = "Value must be scalar.";

    public const STRING_EXPECTED_BUT_FOUND_NON_SCALAR = "String expected but found non-scalar.";

    public const GET_INPUT_PARAMETER_LIST_MUST_RETURN_ARRAY = 'Static function %s::getInputParameterList did not return an array. Must return %s[] ';

    public const MUST_RETURN_ARRAY_OF__INPUT_PARAMETER = 'Static function %s::getInputParameterList Must return array of %s. Item at index %d is wrong type.';

    public const INVALID_JSON_POINTER_FIRST = "First character must be /";

    public const BAD_TYPE_FOR_ARRAY_ACCESS = "Cannot use type [%s] for array access";

    public const CLASS_NOT_FOUND = "Class %s isn't available through auto-loader.";

    public const CLASS_MUST_IMPLEMENT_INPUT_PARAMETER = "Class %s doesn't implement the %s interface. Cannot be used to get array of type.";

    public const MUST_DUPLICATE_PARAMETER = "Must be duplicate of %s";
}
