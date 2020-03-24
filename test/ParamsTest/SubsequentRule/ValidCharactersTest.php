<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use ParamsTest\BaseTestCase;
use Params\ProcessRule\ValidCharacters;
use Params\ProcessRule\SaneCharacters;
use Params\ParamsValuesImpl;

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
        $validator = new ParamsValuesImpl();

        $validationResult = $rule->process('foo', $testValue, $validator);
        if ($expectedErrorPosition !== null) {
            $this->assertNotNull($validationResult->getValidationProblems(), "Failed to detect invalid char at $expectedErrorPosition");

            $this->assertValidationProblemRegexp(
                'foo',
                $validCharactersPattern,
                $validationResult->getValidationProblems()
            );

//            $this->assertStringContainsString(
//                (string)$expectedErrorPosition,
//                $validationResult->getValidationProblems()['/foo']
//            );
//            $this->assertStringContainsString(
//                $validCharactersPattern,
//                $validationResult->getValidationProblems()['/foo']
//            );
        }
        else {
            $this->assertEmpty($validationResult->getValidationProblems());
        }
    }
}
