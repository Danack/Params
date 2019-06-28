<?php

declare(strict_types=1);

namespace ParamsTest\Exception\Validator;

use ParamsTest\BaseTestCase;
use Params\Value\MultipleEnums;
use Params\Value\AddPatchEntry;
use Params\Exception\LogicException;

/**
 * @coversNothing
 * @group patch
 */
class AddPatchEntryTest extends BaseTestCase
{
    /**
     * @covers \Params\Value\AddPatchEntry
     */
    public function testFoo()
    {
        $path = '/a/b/c';
        $value = 5;

        $addPatch = new AddPatchEntry($path, $value);

        $this->assertEquals($path, $addPatch->getPath());
        $this->assertEquals($value, $addPatch->getValue());

        $this->assertEquals(AddPatchEntry::ADD, $addPatch->getOpType());

        $this->expectException(LogicException::class);
        $addPatch->getFrom();
    }
}
