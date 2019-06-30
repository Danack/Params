<?php

declare(strict_types=1);

namespace ParamsTest\Exception\Validator;

use ParamsTest\BaseTestCase;
use Params\Value\MultipleEnums;
use Params\PatchOperation\AddPatchOperation;
use Params\Exception\LogicException;

/**
 * @coversNothing
 * @group patch
 */
class AddPatchEntryTest extends BaseTestCase
{
    /**
     * @covers \Params\PatchOperation\AddPatchOperation
     */
    public function testFoo()
    {
        $path = '/a/b/c';
        $value = 5;

        $addPatch = new AddPatchOperation($path, $value);

        $this->assertEquals($path, $addPatch->getPath());
        $this->assertEquals($value, $addPatch->getValue());

        $this->assertEquals(AddPatchOperation::ADD, $addPatch->getOpType());

        $this->expectException(LogicException::class);
        $addPatch->getFrom();
    }
}
