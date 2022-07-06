<?php

declare(strict_types = 1);

namespace Type\ExtractRule;

use Type\DataStorage\DataStorage;
use Type\Messages;
use Type\OpenApi\ParamDescription;
use Type\ProcessedValues;
use Type\ValidationResult;
use function Type\createArrayOfTypeFromInputStorage;
use function Type\getPropertyDefinitionsForClass;

class GetArrayOfType implements ExtractPropertyRule
{
    /** @var class-string */
    private string $className;

    /** @var \Type\PropertyDefinition[] */
    private array $inputParameterList;

    private GetType $typeExtractor;

    /**
     * @param class-string $className
     */
    public function __construct(string $className)
    {
        $this->className = $className;
        $this->inputParameterList = getPropertyDefinitionsForClass($this->className);

        $this->typeExtractor = GetType::fromClassAndRules(
            $this->className,
            $this->inputParameterList
        );
    }

    public function process(
        ProcessedValues $processedValues,
        DataStorage $dataStorage
    ): ValidationResult {

        // Check it is set
        if ($dataStorage->isValueAvailable() !== true) {
            return ValidationResult::errorResult(
                $dataStorage,
                Messages::ERROR_MESSAGE_NOT_SET_VARIANT_1
            );
        }

        return createArrayOfTypeFromInputStorage(
            $dataStorage,
            $this->typeExtractor
        );
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        // TODO - implement
    }
}
