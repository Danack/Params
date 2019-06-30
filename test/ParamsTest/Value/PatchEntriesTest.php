<?php

declare(strict_types=1);

namespace ParamsTest\Exception\Validator;

use ParamsTest\BaseTestCase;
use Params\Value\PatchOperations;
use Params\PatchOperation\AddPatchOperation;
use Params\PatchOperation\TestPatchOperation;
use Params\Exception\LogicException;

/**
 * @coversNothing
 * @group patch
 */
class PatchEntriesTest extends BaseTestCase
{
    /**
     * @covers \Params\Value\PatchOperations
     */
    public function testBasic()
    {
        $path = '/a/b/c';
        $value = 5;

        $addPatch = new AddPatchOperation($path, $value);
        $testPatch = new TestPatchOperation('/a/b/c', 5);


        $patchEntries = new PatchOperations(...[
            $addPatch,
            $testPatch
        ]);


        $patchEntries = $patchEntries->getPatchOperations();

        $this->assertCount(2, $patchEntries);
        $this->assertEquals($addPatch, $patchEntries[0]);
        $this->assertEquals($testPatch, $patchEntries[1]);
    }
}
