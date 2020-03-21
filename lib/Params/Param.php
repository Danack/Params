<?php

declare(strict_types = 1);

namespace Params;

use Params\ExtractRule\ExtractRule;
use Params\ProcessRule\ProcessRule;
use Params\Exception\BadTypeException;

class Param
{
    /**
     * The name of the input to use.
     */
    private string $input_name;


    /**
     * The rule to extract the parameter from the input.
     */
    private \Params\ExtractRule\ExtractRule $extractRule;

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
        string $input_name,
        ExtractRule $first_rule,
        ProcessRule ...$subsequent_rules
    ) {
        $this->input_name = $input_name;
        $this->extractRule = $first_rule;
        $this->processRules = $subsequent_rules;
    }

    public static function fromType(
        string $input_name,
        string $type_name
    ) {
        $is_correct_type = is_subclass_of(
            $type_name,
            RulesForParamAware::class,
            $allow_string = true
        );

        if ($is_correct_type !== true) {
            throw BadTypeException::fromClassname($type_name);
        }

        /** @var \Params\RulesForParam $rulesForParam  */
        $rulesForParam = call_user_func([$type_name, 'getRulesForParam']);

        return new self(
            $input_name,
            $rulesForParam->getExtractRule(),
            ...$rulesForParam->getProcessRules()
        );
    }

    /**
     * @return string
     */
    public function getInputName(): string
    {
        return $this->input_name;
    }

    /**
     * @return ExtractRule
     */
    public function getFirstRule(): ExtractRule
    {
        return $this->extractRule;
    }

    /**
     * @return ProcessRule[]
     */
    public function getSubsequentRules(): array
    {
        return $this->processRules;
    }
}
