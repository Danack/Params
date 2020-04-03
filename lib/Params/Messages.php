<?php

declare(strict_types = 1);

namespace Params;

class Messages
{
    public const ERROR_DIFFERENT_TYPES = "Parameter %s cannot by the same as %s as they different types, %s and %s.";

    public const ERROR_DIFFERENT_VALUE = "Parameter named '%s' is different to parameter '%s'.";

    public const ERROR_INVALID_DATETIME = 'Value was not a valid RFC3339 date time apparently';

    public const ERROR_MAXIMUM_COUNT_MINIMUM = "Maximum count must be zero or above.";

    public const VALUE_NOT_SET = 'Value not set.';

    public const ERROR_MESSAGE_ITEM_NOT_ARRAY = "Values for type '%s' must be an array, but got '%s'. Use GetArrayOfInt|String for single values.";

    public const ERROR_MESSAGE_NOT_ARRAY = "Value set for '%s' must be an array.";

    public const ERROR_MESSAGE_NOT_ARRAY_VARIANT_1 = "Value must be an array.";

    public const ERROR_MESSAGE_NOT_SET = "Value not set for '%s'.";

    public const ERROR_MESSAGE_NOT_SET_VARIANT_1 = "Value must be set.";

    public const ERROR_MINIMUM_COUNT_MINIMUM = "Minimum count must be zero or above.";

    public const ERROR_NO_PREVIOUS_PARAM = "Param named %s was not previously processed.";

    public const ERROR_TOO_FEW_ELEMENTS = "Number of elements in %s is too small. Min allowed is %d but only got %d.";

    public const ERROR_TOO_MANY_ELEMENTS = "Number of elements in %s is too large. Max allowed is %d but got %d.";

    public const ERROR_WRONG_TYPE = "Minimum count can only be applied to an array but tried to operate on %s.";

    public const ERROR_WRONG_TYPE_VARIANT_1 = "Maximum count can only be applied to an array but tried to operate on %s.";

    public const INVALID_CHAR_MESSAGE = "Invalid character at position %d. Allowed characters are %s";
}
