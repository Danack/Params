<?php

declare(strict_types = 1);


namespace Params;


use Params\DataLocator\InputStorageAye;
use Params\ExtractRule\ExtractRule;
use Params\ProcessRule\ProcessRule;


interface PatchInputParameter
{
    public function setDataStoragePlace(InputStorageAye $inputStorageAye);


    /**
     * @return ExtractRule
     */
    public function getExtractRule(): ExtractRule;

    /**
     * @return ProcessRule[]
     */
    public function getProcessRules(): array;

}

