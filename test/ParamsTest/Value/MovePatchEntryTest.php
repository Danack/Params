<?php

declare(strict_types=1);

namespace ParamsTest\Exception\Validator;

use ParamsTest\BaseTestCase;
use Params\PatchOperation\PatchOperation;
use Params\PatchOperation\MovePatchOperation;
use Params\Exception\LogicException;

/**
 * @coversNothing
 * @group patch
 */
class MovePatchEntryTest extends BaseTestCase
{
    /**
     * @covers \Params\PatchOperation\MovePatchOperation
     */
    public function testFoo()
    {
        $path = '/a/b/c';
        $from = '/d/e/c';

        $addPatch = new MovePatchOperation($path, $from);

        $this->assertEquals($path, $addPatch->getPath());
        $this->assertEquals($from, $addPatch->getFrom());

        $this->assertEquals(PatchOperation::MOVE, $addPatch->getOpType());

        $this->expectException(LogicException::class);
        $addPatch->getValue();
    }
}
