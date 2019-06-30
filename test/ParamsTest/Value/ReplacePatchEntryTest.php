<?php

declare(strict_types=1);

namespace ParamsTest\Exception\Validator;

use ParamsTest\BaseTestCase;
use Params\Value\MultipleEnums;
use Params\PatchOperation\ReplacePatchOperation;
use Params\Exception\LogicException;

/**
 * @coversNothing
 * @group patch
 */
class ReplacePatchEntryTest extends BaseTestCase
{
    /**
     * @covers \Params\PatchOperation\ReplacePatchOperation
     */
    public function testFoo()
    {
        $path = '/a/b/c';
        $value = 5;

        $replacePatch = new ReplacePatchOperation($path, $value);

        $this->assertEquals($path, $replacePatch->getPath());
        $this->assertEquals($value, $replacePatch->getValue());

        $this->assertEquals(ReplacePatchOperation::REPLACE, $replacePatch->getOpType());

        $this->expectException(LogicException::class);
        $replacePatch->getFrom();
        $this->assertEquals('replace', $replacePatch->getOpType());
    }


    /**
     * @covers \Params\PatchOperation\ReplacePatchOperation::getFrom
     */
    public function testGetFromThrows()
    {
        $patch = new ReplacePatchOperation('/a/b/c', 5);
        $this->expectException(\Params\Exception\LogicException::class);
        $patch->getFrom();
    }
}
