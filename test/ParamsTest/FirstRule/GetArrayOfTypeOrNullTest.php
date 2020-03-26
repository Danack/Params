<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use Params\ExtractRule\GetArrayOfTypeOrNull;
use ParamsTest\BaseTestCase;
use Params\ExtractRule\GetArrayOfType;
use ParamsTest\Integration\ItemParams;
use VarMap\ArrayVarMap;
use Params\ParamsValuesImpl;
use Params\Path;

/**
 * @coversNothing
 */
class GetArrayOfTypeOrNullTest extends BaseTestCase
{

    /**
     * @covers \Params\ExtractRule\GetArrayOfTypeOrNull
     * @group debug
     */
    public function testWorks()
    {
        $data = [
            'items' => [
                ['foo' => 5, 'bar' => 'Hello world']
            ],
        ];

        $rule = new GetArrayOfTypeOrNull(ItemParams::class);
        $validator = new ParamsValuesImpl();
        $result = $rule->process(
            Path::fromName('items'),
            new ArrayVarMap($data),
            $validator
        );

        $this->assertFalse($result->isFinalResult());

        $this->assertCount(1, $result->getValue());
        $item = ($result->getValue())[0];
        $this->assertInstanceOf(ItemParams::class, $item);
        /** @var ItemParams $item */
        $this->assertSame(5, $item->getFoo());
        $this->assertSame('Hello world', $item->getBar());

        $this->assertCount(0, $result->getValidationProblems());
    }

    /**
     * @covers \Params\ExtractRule\GetArrayOfTypeOrNull
     */
    public function testWorksWhenNotSet()
    {
        $data = [];

        $rule = new GetArrayOfTypeOrNull(ItemParams::class);
        $validator = new ParamsValuesImpl();
        $result = $rule->process(
            Path::fromName('items'),
            new ArrayVarMap($data),
            $validator
        );

//        $this->assertTrue($result->isFinalResult());
        $this->assertNull($result->getValue());
        $this->assertEmpty($result->getValidationProblems());
    }
}
