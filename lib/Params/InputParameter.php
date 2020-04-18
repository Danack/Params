<?php

declare(strict_types = 1);

namespace Params;

use Params\ExtractRule\ExtractRule;
use Params\ProcessRule\ProcessRule;

/**
 * @todo rename this to InputParameter?
 */
class InputParameter
{
    /**
     * The name of the input to use.
     */
    private string $inputName;

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
