<?php

declare(strict_types = 1);

namespace TypeSpec;

use TypeSpec\ExtractRule\ExtractPropertyRule;
use TypeSpec\ProcessRule\ProcessPropertyRule;

/**
 * The definition of how a property of a type should be extracted
 * and processed.
 */
class InputTypeSpec // InputTypeSpec
{
    /**
     * The name of the input to use.
     */
    private string $inputName;

    private ?string $target_parameter_name = null;

    /**
     * The rule to extract the parameter from the input.
     */
    private ExtractPropertyRule $extractRule;

    /**
     * The subsequent rules to process the parameter.
     * @var \TypeSpec\ProcessRule\ProcessPropertyRule[]
     */
    private array $processRules;

    /**
     *
     * @param string $input_name
     * @param ExtractPropertyRule $first_rule
     * @param ProcessPropertyRule ...$subsequent_rules
     */
    public function __construct(
        string              $input_name, // TODO - this should be a locator component...
        ExtractPropertyRule $first_rule,
        ProcessPropertyRule ...$subsequent_rules
    ) {
        $this->inputName = $input_name;
        $this->extractRule = $first_rule;
        $this->processRules = $subsequent_rules;
    }

//    private static function withoutTargetName(
//        string              $input_name, // TODO - this should be a locator component...
//        ExtractPropertyRule $first_rule,
//        ProcessPropertyRule ...$subsequent_rules
//    ) {
//        $this->inputName = $input_name;
//        $this->extractRule = $first_rule;
//        $this->processRules = $subsequent_rules;
//    }
//
//    private static function createComplete(
//        string              $input_name, // TODO - this should be a locator component...
//        string              $target_parameter_name,
//        ExtractPropertyRule $first_rule,
//        ProcessPropertyRule ...$subsequent_rules
//    ) {
//        $this->inputName = $input_name;
//        $this->target_parameter_name = $target_parameter_name;
//        $this->extractRule = $first_rule;
//        $this->processRules = $subsequent_rules;
//        $this->target_parameter_name = $name;
//    }

    public function setTargetParameterName(string $name): void
    {
        $this->target_parameter_name = $name;
    }

    /**
     * @return string
     */
    public function getTargetParameterName(): string
    {
        if ($this->target_parameter_name === null) {
            return $this->inputName;
        }

        return $this->target_parameter_name;
    }

    /**
     * @return string
     */
    public function getInputName(): string
    {
        return $this->inputName;
    }

    /**
     * @return ExtractPropertyRule
     */
    public function getExtractRule(): ExtractPropertyRule
    {
        return $this->extractRule;
    }

    /**
     * @return ProcessPropertyRule[]
     */
    public function getProcessRules(): array
    {
        return $this->processRules;
    }
}
