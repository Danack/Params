<?php

declare(strict_types=1);

namespace ParamsTest\Exception\Validator;

use ParamsTest\BaseTestCase;
use Params\PatchOperation\TestPatchOperation;
use Params\Exception\LogicException;

/**
 * @coversNothing
 * @group patch
 */
class TestPatchEntryTest extends BaseTestCase
{
    /**
     * @covers \Params\PatchOperation\TestPatchOperation
     */
    public function testFoo()
    {
        $path = '/a/b/c';
        $value = 5;

        $patch = new TestPatchOperation($path, $value);

        $this->assertEquals($path, $patch->getPath());
        $this->assertEquals($value, $patch->getValue());

        $this->assertEquals(TestPatchOperation::TEST, $patch->getOpType());

        $this->expectException(LogicException::class);
        $patch->getFrom();

        $this->assertEquals('test', $patch->getOpType());
    }


    /**
     * @covers \Params\PatchOperation\TestPatchOperation::getFrom
     */
    public function testGetFromThrows()
    {
        $patch = new TestPatchOperation('/a/b/c', 5);
        $this->expectException(\Params\Exception\LogicException::class);
        $patch->getFrom();
    }
}
