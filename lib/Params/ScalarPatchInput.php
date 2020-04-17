<?php

declare(strict_types = 1);

namespace Params;

use Params\DataLocator\InputStorageAye;
use Params\ExtractRule\ExtractRule;
use Params\ProcessRule\ProcessRule;

class ScalarPatchInput implements PatchInputParameter
{
    /**
     * The rule to extract the parameter from the input.
     */
    private ExtractRule $extractRule;

    /**
     * The subsequent rules to process the parameter.
     * @var \Params\ProcessRule\ProcessRule[]
     */
    private array $processRules;

    public function __construct(
        ExtractRule $first_rule,
        ProcessRule ...$subsequent_rules
    ) {
        $this->extractRule = $first_rule;
        $this->processRules = $subsequent_rules;
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

    public function setDataStoragePlace(InputStorageAye $inputStorageAye): InputStorageAye
    {
        // nothing to do.
        return $inputStorageAye;
    }
}
