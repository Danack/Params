<?php

declare(strict_types = 1);

namespace Type\ProcessRule;

use Type\DataStorage\DataStorage;
use Type\Messages;
use Type\OpenApi\ParamDescription;
use Type\ProcessedValues;
use Type\ValidationResult;
use Type\Value\OrderElement;
use Type\Value\Ordering;
use function Type\array_value_exists;
use function Type\normalise_order_parameter;

/**
 * Class Order
 *
 * Supports a parameter to specify ordering of results
 * For example "+name,-date" would be equivalent to ordering
 * by name ascending, then date descending.
 */
class Order implements ProcessPropertyRule
{
    use CheckString;

    /** @var string[] */
    private array $knownOrderNames;

    /**
     * OrderValidator constructor.
     * @param string[] $knownOrderNames
     */
    public function __construct(array $knownOrderNames)
    {
        $this->knownOrderNames = $knownOrderNames;
    }

    public function process(
        $value,
        ProcessedValues $processedValues,
        DataStorage $inputStorage
    ): ValidationResult {

        $value = $this->checkString($value);

        $parts = explode(',', $value);
        $orderElements = [];

        foreach ($parts as $part) {
            list($partName, $partOrder) = normalise_order_parameter($part);
            if (array_value_exists($this->knownOrderNames, $partName) !== true) {
                $message = sprintf(
                    Messages::ORDER_VALUE_UNKNOWN,
                    $partName,
                    implode(', ', $this->knownOrderNames)
                );

                return ValidationResult::errorResult($inputStorage, $message);
            }
            $orderElements[] = new OrderElement($partName, $partOrder);
        }

        return ValidationResult::valueResult(new Ordering($orderElements));
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setType(ParamDescription::TYPE_ARRAY);
        $paramDescription->setCollectionFormat(ParamDescription::COLLECTION_CSV);
    }
}
