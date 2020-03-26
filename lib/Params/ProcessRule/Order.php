<?php

declare(strict_types=1);

namespace Params\ProcessRule;

use Params\ValidationResult;
use Params\Value\OrderElement;
use Params\Functions;
use Params\Value\Ordering;
use Params\OpenApi\ParamDescription;
use Params\ParamsValuesImpl;
use Params\ParamValues;
use Params\Path;

/**
 * Class Order
 *
 * Supports a parameter to specify ordering of results
 * For example "+name,-date" would be equivalent to ordering
 * by name ascending, then date descending.
 */
class Order implements ProcessRule
{
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

    public function process(Path $path, $value, ParamValues $validator) : ValidationResult
    {
        $parts = explode(',', $value);
        $orderElements = [];

        foreach ($parts as $part) {
            list($partName, $partOrder) = Functions::normalise_order_parameter($part);
            if (Functions::array_value_exists($this->knownOrderNames, $partName) !== true) {
                $message = sprintf(
                    "Cannot order by [%s] for [%s], as not known for this operation. Known are [%s]",
                    $partName,
                    $path->toString(),
                    implode(', ', $this->knownOrderNames)
                );

                return ValidationResult::errorResult($path, $message);
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
