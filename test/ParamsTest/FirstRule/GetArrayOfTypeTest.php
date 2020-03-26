<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use ParamsTest\BaseTestCase;
use Params\ExtractRule\GetArrayOfType;
use ParamsTest\Integration\ItemParams;
use VarMap\ArrayVarMap;
use Params\ParamsValuesImpl;
use ParamsTest\Integration\SingleIntParams;
use Params\Path;

/**
 * @coversNothing
 */
class GetArrayOfTypeTest extends BaseTestCase
{

    /**
     * @covers \Params\ExtractRule\GetArrayOfType
     */
    public function testWorks()
    {
        $data = [
            'items' => [
                ['foo' => 5, 'bar' => 'Hello world']
            ],
        ];

        $rule = new GetArrayOfType(ItemParams::class);
        $validator = new ParamsValuesImpl();
        $result = $rule->process(
            Path::fromName('items'),
            new ArrayVarMap($data),
            $validator
        );

        $this->assertFalse($result->isFinalResult());
//        $this->assertEquals("Value not set for 'items'.", $result->getProblemMessages());

        $this->assertCount(1, $result->getValue());
        $item = ($result->getValue())[0];
        $this->assertInstanceOf(ItemParams::class, $item);
        /** @var ItemParams $item */
        $this->assertSame(5, $item->getFoo());
        $this->assertSame('Hello world', $item->getBar());

        $this->assertCount(0, $result->getValidationProblems());
    }



    /**
     * @covers \Params\ExtractRule\GetArrayOfType
     */
    public function testMissingArrayErrors()
    {
        $data = [];

        $rule = new GetArrayOfType(ItemParams::class);
        $validator = new ParamsValuesImpl();
        $result = $rule->process(
            Path::fromName('items'),
            new ArrayVarMap($data),
            $validator
        );
        $this->assertTrue($result->isFinalResult());
        $expectedKey = '/items';

        $problems = $result->getValidationProblems();
        $this->assertCount(1, $problems);
        $firstProblem = $problems[0];

        $this->assertEquals("Value must be set.", $firstProblem->getProblemMessage());
        $this->assertNull($result->getValue());
    }


    /**
     * @covers \Params\ExtractRule\GetArrayOfType
     */
    public function testScalarInsteadOfArrayErrors()
    {
        $data = [
            'items' => 'a banana'
        ];

        $rule = new GetArrayOfType(ItemParams::class);
        $validator = new ParamsValuesImpl();
        $result = $rule->process(
            Path::fromName('items'),
            new ArrayVarMap($data),
            $validator
        );
        $this->assertTrue($result->isFinalResult());
        $expectedKey = 'items';

        $problems = $result->getValidationProblems();

        $this->assertCount(1, $problems);
        $this->assertValidationProblem(
            $expectedKey,
            "Value must be an array.",
            $problems
        );

        $this->assertNull($result->getValue());
    }

    /**
     * @covers \Params\ExtractRule\GetArrayOfType
     */
    public function testScalarInsteadOfEntryArrayErrors()
    {
        $data = [
            'items' => [
                // wrong - should be ['limit' => 5]
                5
            ]
        ];

        $rule = new GetArrayOfType(SingleIntParams::class);
        $validator = new ParamsValuesImpl();
        $result = $rule->process(
            Path::fromName('items'),
            new ArrayVarMap($data),
            $validator
        );
        $this->assertTrue($result->isFinalResult());

//        $expectedKey = 'items';

        $validationProblems = $result->getValidationProblems();

        $this->assertCount(1, $validationProblems);

        $this->assertValidationProblemRegexp(
            'items',
            GetArrayOfType::ERROR_MESSAGE_ITEM_NOT_ARRAY,
            $validationProblems
        );

        $this->assertNull($result->getValue());
    }



    /**
     * @covers \Params\ExtractRule\GetArrayOfType
     */
    public function testSingleError()
    {
        $data = [
            'items' => [
                ['foo' => 5, 'bar' => false]
            ],
        ];

        $rule = new GetArrayOfType(ItemParams::class);
        $validator = new ParamsValuesImpl();
        $result = $rule->process(
            Path::fromName('items'),
            new ArrayVarMap($data),
            $validator
        );

        $this->assertTrue($result->isFinalResult());
        $this->assertNull($result->getValue());

        $this->assertCount(1, $result->getValidationProblems());

//        $this->markTestSkipped("This needs fixing");

        $this->assertValidationProblem(
            'items[0]/bar',
            "String too short, min chars is 4",
            $result->getValidationProblems()
        );
    }

    /**
     * @covers \Params\ExtractRule\GetArrayOfType
     */
    public function testMultipleErrors()
    {
        $data = [
            'items' => [
                ['foo' => 5, 'bar' => 'foo'],
                ['foo' => 101, 'bar' => 'world']
            ],
        ];

        $validator = new ParamsValuesImpl();
        $rule = new GetArrayOfType(ItemParams::class);
        $result = $rule->process(
            Path::fromName('items'),
            new ArrayVarMap($data),
            $validator
        );

        $this->assertTrue($result->isFinalResult());
        $this->assertNull($result->getValue());

        $validationProblems = $result->getValidationProblems();
        $this->assertCount(2, $validationProblems);

        $this->assertValidationProblem(
            'items[0]/bar',
            "String too short, min chars is 4",
            $validationProblems
        );

        $this->assertValidationProblem(
            'items[1]/foo',
            "Value too large. Max allowed is 100",
            $validationProblems
        );
    }
}
