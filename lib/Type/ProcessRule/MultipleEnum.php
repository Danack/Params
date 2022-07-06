<?php

declare(strict_types = 1);

namespace Type\ProcessRule;

use Type\DataStorage\DataStorage;
use Type\Messages;
use Type\OpenApi\ParamDescription;
use Type\ProcessedValues;
use Type\ValidationResult;
use Type\Value\MultipleEnums;
use function Type\array_value_exists;

/**
 * Checks whether a string represent a valid multiple enum string e.g.
 *
 * Say we have an endpoint for downloading information about content. The users can select
 * from video, audio, pdf, excel
 *
 * The string "video,audio" would indicate the user wanted to see content of type video or audio
 */
class MultipleEnum implements ProcessPropertyRule
{
    use CheckString;

    /** @var string[] */
    private array $allowedValues;

    /**
     * @param string[] $allowedValues
     */
    public function __construct(array $allowedValues)
    {
        $this->allowedValues = $allowedValues;
    }

    public function process(
        $value,
        ProcessedValues $processedValues,
        DataStorage $inputStorage
    ): ValidationResult {

        $value = $this->checkString($value);

        $value = trim($value);
        $enumStringParts = explode(',', $value);
        $enumElements = [];
        foreach ($enumStringParts as $enumStringPart) {
//            $enumStringPart = trim($enumStringPart);
            if (strlen($enumStringPart) === 0) {
                // TODO - needs unit test.
                // treat empty segments as no value
                continue;
            }

            if (array_value_exists($this->allowedValues, $enumStringPart) !== true) {
                $message = sprintf(
                    Messages::ENUM_MAP_UNRECOGNISED_VALUE_MULTIPLE,
                    $enumStringPart,
                    implode(', ', $this->allowedValues)
                );

                return ValidationResult::errorResult($inputStorage, $message);
            }
            $enumElements[] = $enumStringPart;
        }

        return ValidationResult::valueResult(new MultipleEnums($enumElements));
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setType(ParamDescription::TYPE_ARRAY);
        $paramDescription->setCollectionFormat(ParamDescription::COLLECTION_CSV);
        $paramDescription->setEnum($this->allowedValues);
    }
}
