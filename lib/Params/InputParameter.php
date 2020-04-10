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


//    public static function fromType(
//        string $input_name,
//        string $type_name
//    ) {
//        $is_correct_type = is_subclass_of(
//            $type_name,
//            ParamAware::class,
//            $allow_string = true
//        );
//
//        if ($is_correct_type !== true) {
//            throw BadTypeException::fromClassname($type_name);
//        }
//
//        /** @var \Params\ParamAware $rulesForParam  */
//        $rulesForParam = call_user_func([$type_name, 'getParamInfo'], $input_name);
//
//        var_dump($rulesForParam);
//        exit(0);
//
//        return new self(
//            $input_name,
//            $rulesForParam->getExtractRule(),
//            ...$rulesForParam->getProcessRules()
//        );
//    }

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
