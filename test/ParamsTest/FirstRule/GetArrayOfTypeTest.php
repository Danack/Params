<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use ParamsTest\BaseTestCase;
use Params\ExtractRule\GetArrayOfType;
use ParamsTest\Integration\ItemParams;
use VarMap\ArrayVarMap;
use Params\ParamsValidator;
use ParamsTest\Integration\SingleIntParams;

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
        $validator = new ParamsValidator();
        $result = $rule->process('items', new ArrayVarMap($data), $validator);

        $this->assertFalse($result->isFinalResult());
//        $this->assertEquals("Value not set for 'items'.", $result->getProblemMessages());

        $this->assertCount(1, $result->getValue());
        $item = ($result->getValue())[0];
        $this->assertInstanceOf(ItemParams::class, $item);
        /** @var ItemParams $item */
        $this->assertSame(5, $item->getFoo());
        $this->assertSame('Hello world', $item->getBar());

        $this->assertCount(0, $result->getProblemMessages());
    }



    /**
     * @covers \Params\ExtractRule\GetArrayOfType
     */
    public function testMissingArrayErrors()
    {
        $data = [];

        $rule = new GetArrayOfType(ItemParams::class);
        $validator = new ParamsValidator();
        $result = $rule->process('items', new ArrayVarMap($data), $validator);
        $this->assertTrue($result->isFinalResult());
        $expectedKey = '/items';
        $this->assertEquals("Value must be set.", $result->getProblemMessages()[$expectedKey]);
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
        $validator = new ParamsValidator();
        $result = $rule->process('items', new ArrayVarMap($data), $validator);
        $this->assertTrue($result->isFinalResult());
        $expectedKey = '/items';
        $this->assertEquals(
            "Value must be an array.",
            $result->getProblemMessages()[$expectedKey]
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
        $validator = new ParamsValidator();
        $result = $rule->process('items', new ArrayVarMap($data), $validator);
        $this->assertTrue($result->isFinalResult());

        $expectedKey = '/items';
        $this->assertRegExp(
            stringToRegexp(GetArrayOfType::ERROR_MESSAGE_ITEM_NOT_ARRAY),
            $result->getProblemMessages()[$expectedKey]
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
        $validator = new ParamsValidator();
        $result = $rule->process('items', new ArrayVarMap($data), $validator);

        $this->assertTrue($result->isFinalResult());
        $this->assertNull($result->getValue());

        $expectedKey = '/items/0/bar';
        $problems = $result->getProblemMessages();
        $this->assertArrayHasKey($expectedKey, $problems);

        $this->assertSame(
            "String too short, min chars is 4",
            $problems[$expectedKey]
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

        $validator = new ParamsValidator();
        $rule = new GetArrayOfType(ItemParams::class);
        $result = $rule->process('items', new ArrayVarMap($data), $validator);

        $this->assertTrue($result->isFinalResult());
        $this->assertNull($result->getValue());

        $expectedErrors = [
            '/items/0/bar' => "String too short, min chars is 4",
            '/items/1/foo' => "Value too large. Max allowed is 100"
        ];

        $this->assertSame(
            $expectedErrors,
            $result->getProblemMessages()
        );
    }
}
