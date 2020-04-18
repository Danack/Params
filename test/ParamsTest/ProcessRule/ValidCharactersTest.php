<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Params\DataLocator\DataStorage;
use ParamsTest\BaseTestCase;
use Params\ProcessRule\ValidCharacters;
use Params\ProcessRule\SaneCharacters;
use Params\ProcessedValuesImpl;

/**
 * @coversNothing
 */
class ValidCharactersTest extends BaseTestCase
{
    public function provideTestCases()
    {
        return [
            ['a-zA-Z', 'john', null],
            ['a-zA-Z', 'johnny-5', 6],  // bad digit and hyphen
            ['a-zA-Z', 'jo  hn', 2], // bad space

            [implode(SaneCharacters::ALLOWED_CHAR_TYPES), "jo.hn", null], //punctuation is not letter or number
        ];
    }

    /**
     * @dataProvider provideTestCases
     * @covers \Params\ProcessRule\ValidCharacters
     */
    public function testValidation($validCharactersPattern, $testValue, $expectedErrorPosition)
    {
        $rule = new ValidCharacters($validCharactersPattern);
        $processedValues = new ProcessedValuesImpl();
        $dataLocator = DataStorage::fromArraySetFirstValue([]);

        $validationResult = $rule->process(
            $testValue, $processedValues, $dataLocator
        );
        if ($expectedErrorPosition !== null) {
            $this->assertExpectedValidationProblems(
                $validationResult->getValidationProblems(),
                "Failed to detect invalid char at $expectedErrorPosition"
            );

            $this->assertValidationProblemRegexp(
                '/',
                $validCharactersPattern,
                $validationResult->getValidationProblems()
            );
        }
        else {
            $this->assertNoValidationProblems($validationResult->getValidationProblems());
        }
    }
}
