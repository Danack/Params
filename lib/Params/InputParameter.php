<?php

declare(strict_types = 1);

namespace Params;

use Params\ExtractRule\ExtractRule;
use Params\ProcessRule\ProcessRule;

/**
 *
 */
class InputParameter
{
    /**
     * The name of the input to use.
     */
    private string $inputName;

    private ?string $target_parameter_name = null;

    /**
     * The rule to extract the parameter from the input.
     */
    private ExtractRule $extractRule;

    /**
     * The subsequent rules to process the parameter.
     * @var \Params\ProcessRule\ProcessRule[]
     */
    private array $processRules;

    /**
     *
     * @param string $input_name
     * @param ExtractRule $first_rule
     * @param ProcessRule ...$subsequent_rules
     */
    public function __construct(
        string $input_name, // TODO - this should be a locator component...
        ExtractRule $first_rule,
        ProcessRule ...$subsequent_rules
    ) {
        $this->inputName = $input_name;
        $this->extractRule = $first_rule;
        $this->processRules = $subsequent_rules;
    }


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
     * @return ExtractRule
     */
    public function getExtractRule(): ExtractRule
    {
        return $this->extractRule;
    }

    /**
     * @return ProcessRule[]
     */
    public function getProcessRules(): array
    {
        return $this->processRules;
    }
}
