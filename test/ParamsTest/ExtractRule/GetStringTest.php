<?php

declare(strict_types=1);

namespace ParamsTest\ExtractRule;

use Params\DataLocator\NotAvailableInputStorageAye;
use Params\Messages;
use ParamsTest\BaseTestCase;
use Params\ExtractRule\GetString;
use Params\ProcessedValuesImpl;
use Params\DataLocator\DataStorage;

/**
 * @coversNothing
 */
class GetStringTest extends BaseTestCase
{
    /**
     * @covers \Params\ExtractRule\GetString
     */
    public function testMissingGivesError()
    {
        $rule = new GetString();
        $validator = new ProcessedValuesImpl();
        $validationResult = $rule->process(
            $validator, new NotAvailableInputStorageAye()
        );
        $this->assertExpectedValidationProblems($validationResult->getValidationProblems());
    }

    /**
     * @covers \Params\ExtractRule\GetString
     */
    public function testValidation()
    {
        $expectedValue = 'John';

        $rule = new GetString();
        $validator = new ProcessedValuesImpl();
        $validationResult = $rule->process(
            $validator,
            DataStorage::fromArraySetFirstValue([$expectedValue])
        );

        $this->assertNoValidationProblems($validationResult->getValidationProblems());
        $this->assertEquals($validationResult->getValue(), $expectedValue);
    }

    /**
     * @covers \Params\ExtractRule\GetString
     */
    public function testFromArrayErrors()
    {
        $index = 'foo';

        $data = [$index => [1, 2, 3]];

        $rule = new GetString();
        $validator = new ProcessedValuesImpl();
        $validationResult = $rule->process(
            $validator,
            DataStorage::fromArraySetFirstValue($data)
        );

        $this->assertValidationProblemRegexp(
            '/' . $index,
            Messages::STRING_EXPECTED_BUT_FOUND_NON_SCALAR,
            $validationResult->getValidationProblems()
        );
    }
}
