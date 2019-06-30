<?php

declare(strict_types=1);

namespace ParamsTest\Exception\Validator;

use ParamsTest\BaseTestCase;
use Params\PatchOperation\PatchOperation;
use Params\PatchOperation\RemovePatchOperation;
use Params\Exception\LogicException;

/**
 * @coversNothing
 * @group patch
 */
class RemovePatchEntryTest extends BaseTestCase
{
    /**
     * @covers \Params\PatchOperation\RemovePatchOperation
     */
    public function testBasic()
    {
        $path = '/a/b/c';
        $removePatch = new RemovePatchOperation($path);

        $this->assertEquals($path, $removePatch->getPath());
        $this->assertEquals(PatchOperation::REMOVE, $removePatch->getOpType());

        try {
            $removePatch->getFrom();
            $this->fail('getFrom failed to throw LogicException');
        }
        catch (LogicException $le) {
            $this->assertTrue(true);
        }

        $this->expectException(LogicException::class);
        $removePatch->getValue();

        $this->assertEquals("remove", $removePatch->getOpType());
    }

    /**
     * @covers \Params\PatchOperation\RemovePatchOperation::getFrom
     */
    public function testGetFromThrows()
    {
        $removePatch = new RemovePatchOperation('/a/b/c');
        $this->expectException(\Params\Exception\LogicException::class);
        $removePatch->getFrom();
    }

    /**
     * @covers \Params\PatchOperation\RemovePatchOperation::getValue
     */
    public function testGetValueThrows()
    {
        $removePatch = new RemovePatchOperation('/a/b/c');
        $this->expectException(\Params\Exception\LogicException::class);
        $removePatch->getValue();
    }
}
