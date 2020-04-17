<?php

declare(strict_types = 1);

namespace ParamsTest\Exception\Validator;

use Params\DataLocator\InputStorageAye;
use Params\ExtractRule\ExtractRule;
use Params\ExtractRule\GetString;
use Params\JsonPatchInputParameter;
use Params\PatchInputParameter;
use Params\ProcessedValuesImpl;
use Params\DataLocator\DataStorage;
use Params\ScalarPatchInput;

use Params\ProcessRule\ProcessRule;
use function Params\processPatchInputParameter;

class ProcessPatchInputParameterTest
{
    /**
     * @group patch
     */
    public function testWorks()
    {
        $param = new ScalarPatchInput(
            new GetString()
        );

        $paramValues = new ProcessedValuesImpl();
        $dataLocator = DataStorage::fromSingleValueButRoot('message', 'Hello world');

        $result = processPatchInputParameter(
            $param,
            $paramValues,
            $dataLocator
        );

        var_dump($result);
    }
}
