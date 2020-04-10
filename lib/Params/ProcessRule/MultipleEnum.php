<?php

declare(strict_types = 1);

namespace Params\ProcessRule;

use Params\DataLocator\InputStorageAye;
use Params\Messages;
use Params\OpenApi\ParamDescription;
use Params\ProcessedValues;
use Params\ValidationResult;
use Params\Value\MultipleEnums;
use function Params\array_value_exists;

/**
 * Checks whether a string represent a valid multiple enum string e.g.
 *
 * Say we have an endpoint for downloading information about content. The users can select
 * from video, audio, pdf, excel
 *
 * The string "video,audio" would indicate the user wanted to see content of type video or audio
 */
class MultipleEnum implements ProcessRule
{
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
        InputStorageAye $dataLocator
    ): ValidationResult {
        // TODO - handle to string conversion better.
        $value = trim((string)$value);
        $filterStringParts = explode(',', $value);
        $filterElements = [];
        foreach ($filterStringParts as $filterStringPart) {
            $filterStringPart = trim($filterStringPart);
            if (strlen($filterStringPart) === 0) {
                // TODO - needs unit test.
                // treat empty segments as no value
                continue;
            }

            if (array_value_exists($this->allowedValues, $filterStringPart) !== true) {
                $message = sprintf(
                    Messages::MULTIPLE_ENUM_INVALID,
                    $filterStringPart,
                    implode(', ', $this->allowedValues)
                );

                return ValidationResult::errorResult($dataLocator, $message);
            }
            $filterElements[] = $filterStringPart;
        }

        return ValidationResult::valueResult(new MultipleEnums($filterElements));
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setType(ParamDescription::TYPE_ARRAY);
        $paramDescription->setCollectionFormat(ParamDescription::COLLECTION_CSV);
        $paramDescription->setEnum($this->allowedValues);
    }
}
