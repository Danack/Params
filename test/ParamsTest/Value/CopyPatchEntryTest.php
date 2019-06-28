<?php

declare(strict_types=1);

namespace ParamsTest\Exception\Validator;

use ParamsTest\BaseTestCase;
use Params\Value\PatchEntry;
use Params\Value\CopyPatchEntry;
use Params\Exception\LogicException;

/**
 * @coversNothing
 * @group patch
 */
class CopyPatchEntryTest extends BaseTestCase
{
    /**
     * @covers \Params\Value\CopyPatchEntry
     */
    public function testFoo()
    {
        $path = '/a/b/c';
        $from = '/d/e/c';

        $patch = new CopyPatchEntry($path, $from);

        $this->assertEquals($path, $patch->getPath());
        $this->assertEquals($from, $patch->getFrom());

        $this->assertEquals(PatchEntry::COPY, $patch->getOpType());

        $this->expectException(LogicException::class);
        $patch->getValue();

        $this->assertEquals('copy', $patch->getOpType());
    }


    /**
     * @covers \Params\Value\CopyPatchEntry::getValue
     */
    public function testGetValueThrows()
    {
        $patch = new CopyPatchEntry('/a/b/c', '/d/e/f');
        $this->expectException(\Params\Exception\LogicException::class);
        $patch->getValue();
    }
}
