<?php

declare(strict_types = 1);

namespace ParamsTest\Integration;

use Params\Create\CreateOrErrorFromArray;
use ParamsTest\BaseTestCase;
use ParamsTest\Integration\IntArrayParams;
use function Params\createPath;

/**
 * @coversNothing
 */
class ErrorPathsTest extends BaseTestCase
{
    public function testErrorInSecondArrayElement()
    {
        $data = [
            'name' => 'Johnahan',
            'counts' => [3, 4]
        ];
        $errors = [
            createPath(['name' => 'counts']) => 'Value at position [1] is not a multiple of 3 but has value [4]'
        ];

        $this->executeTest($data, $errors);
    }


    private function executeTest($data, $expectedErrors)
    {

        [$intArrayParams, $errors] = IntArrayParams::createOrErrorFromArray($data);

        $this->assertIsArray($errors);
        $this->assertCount(count($expectedErrors), $errors);

        foreach ($expectedErrors as $key => $expectedErrorMessage) {
            $this->assertValidationProblem(
                $key,
                $expectedErrorMessage,
                $errors
            );
        }
    }
}
