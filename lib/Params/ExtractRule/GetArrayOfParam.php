<?php

declare(strict_types = 1);

namespace Params\ExtractRule;

use Params\DataStorage\DataStorage;
use Params\Messages;
use Params\OpenApi\ParamDescription;
use Params\ProcessedValues;
use Params\ValidationResult;
use Params\Param;
use function Params\createArrayOfTypeFromInputStorage;
use function Params\getParamForClass;

class GetArrayOfParam implements ExtractRule
{
    /** @var class-string */
    private string $className;

//    private Param $param;

    private GetType $typeExtractor;

    /**
     * @param class-string $className
     */
    public function __construct(string $className)
    {
        $this->className = $className;

        //$this->param = getParamForClass($this->className);
        $inputParameterList = getParamForClass($this->className);

        $this->typeExtractor = GetType::fromClassAndRules(
            $this->className,
            $inputParameterList
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
