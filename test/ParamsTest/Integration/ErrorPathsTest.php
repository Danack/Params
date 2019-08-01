<?php

declare(strict_types = 1);

namespace ParamsTest\Integration;

use Params\Create\CreateOrErrorFromArray;
use ParamsTest\BaseTestCase;
use ParamsTest\Integration\IntArrayParams;

/**
 * @coversNothing
 */
class ErrorPathsTest extends BaseTestCase
{
//    public function testErrorInName()
//    {
//        $data = [
//            'name' => 'No',
//            'counts' => [3]
//        ];
//        $errors = [
//            '/name' => "string for 'name' too short, min chars is 4"
//        ];
//
//        $this->executeTest($data, $errors);
//    }

    public function testErrorInSecondArrayElement()
    {
        $data = [
            'name' => 'Johnahan',
            'counts' => [3, 4]
        ];
        $errors = [
            "/counts/1" => 'Value is not a multiple of 3 but has value [4]'
        ];

        $this->executeTest($data, $errors);
    }


    private function executeTest($data, $expectedErrors)
    {
        [$intArrayParams, $errors] = IntArrayParams::createOrErrorFromArray($data);

        $this->assertIsArray($errors);
        $this->assertCount(count($expectedErrors), $errors);

        foreach ($expectedErrors as $key => $expectedErrorMessage) {
            $this->assertArrayHasKey($key, $errors, "Actual contents: " . json_encode($errors));
            $errorMessage = $errors[$key];
            $this->assertSame($expectedErrorMessage, $errorMessage);
        }
    }
}
