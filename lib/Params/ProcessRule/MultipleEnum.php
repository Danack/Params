<?php

declare(strict_types=1);

namespace Params\ProcessRule;

use Params\ValidationResult;
use Params\Value\MultipleEnums;
use Params\Functions;
use Params\OpenApi\ParamDescription;
use Params\ParamsValidator;
use Params\ParamValues;

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

    public function process(string $name, $value, ParamValues $validator) : ValidationResult
    {
        $value = trim($value);
        $filterStringParts = explode(',', $value);
        $filterElements = [];
        foreach ($filterStringParts as $filterStringPart) {
            $filterStringPart = trim($filterStringPart);
            if (strlen($filterStringPart) === 0) {
                // TODO - needs unit test.
                // treat empty segments as no value
                continue;
            }

            if (Functions::array_value_exists($this->allowedValues, $filterStringPart) !== true) {
                $message = sprintf(
                    "Cannot filter by [%s] for [%s], as not known for this operation. Known are [%s]",
                    $filterStringPart,
                    $name,
                    implode(', ', $this->allowedValues)
                );

                return ValidationResult::errorResult($name, $message);
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
