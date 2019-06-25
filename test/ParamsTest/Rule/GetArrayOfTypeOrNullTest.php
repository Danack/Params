<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use Params\Rule\GetArrayOfTypeOrNull;
use ParamsTest\BaseTestCase;
use Params\Rule\GetArrayOfType;
use ParamsTest\ItemParams;
use VarMap\ArrayVarMap;

/**
 * @coversNothing
 * @group wip
 */
class GetArrayOfTypeOrNullTest extends BaseTestCase
{

    /**
     * @covers \Params\Rule\GetArrayOfType
     */
    public function testWorks()
    {
        $data = [
            'items' => [
                ['foo' => 5, 'bar' => 'Hello world']
            ],
        ];

        $rule = new GetArrayOfTypeOrNull(new ArrayVarMap($data), ItemParams::class);
        $result = $rule('items', 5);

        $this->assertFalse($result->isFinalResult());

        $this->assertCount(1, $result->getValue());
        $item = ($result->getValue())[0];
        $this->assertInstanceOf(ItemParams::class, $item);
        /** @var ItemParams $item */
        $this->assertSame(5, $item->getFoo());
        $this->assertSame('Hello world', $item->getBar());

        $this->assertNull($result->getProblemMessage());
    }

    /**
     * @covers \Params\Rule\GetArrayOfType
     */
    public function testWorksWhenNotSet()
    {
        $data = [];

        $rule = new GetArrayOfTypeOrNull(new ArrayVarMap($data), ItemParams::class);
        $result = $rule('items', 5);

        $this->assertTrue($result->isFinalResult());
        $this->assertNull($result->getValue());
        $this->assertNull($result->getProblemMessage());
    }
}
