<?php

declare(strict_types=1);

namespace ParamsTest\Exception\Validator;

use ParamsTest\BaseTestCase;
use Params\PatchOperation\PatchOperation;
use Params\PatchOperation\CopyPatchOperation;
use Params\Exception\LogicException;

/**
 * @coversNothing
 * @group patch
 */
class CopyPatchEntryTest extends BaseTestCase
{
    /**
     * @covers \Params\PatchOperation\CopyPatchOperation
     */
    public function testFoo()
    {
        $path = '/a/b/c';
        $from = '/d/e/c';

        $patch = new CopyPatchOperation($path, $from);

        $this->assertEquals($path, $patch->getPath());
        $this->assertEquals($from, $patch->getFrom());

        $this->assertEquals(PatchOperation::COPY, $patch->getOpType());

        $this->expectException(LogicException::class);
        $patch->getValue();

        $this->assertEquals('copy', $patch->getOpType());
    }


    /**
     * @covers \Params\PatchOperation\CopyPatchOperation::getValue
     */
    public function testGetValueThrows()
    {
        $patch = new CopyPatchOperation('/a/b/c', '/d/e/f');
        $this->expectException(\Params\Exception\LogicException::class);
        $patch->getValue();
    }
}
