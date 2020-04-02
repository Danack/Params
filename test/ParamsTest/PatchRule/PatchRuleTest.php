<?php

declare(strict_types = 1);

namespace ParamsTest\PatchRule;

use Params\PatchRule\PatchAdd;
use Params\PatchRule\PatchCopy;
use Params\PatchRule\PatchMove;
use Params\PatchRule\PatchRemove;
use Params\PatchRule\PatchReplace;
use Params\PatchRule\PatchTest;
use ParamsTest\BaseTestCase;
use ParamsTest\Patch\Sku\SkuPriceAdd;

/**
 * @coversNothing
 */
class PatchRuleTest extends BaseTestCase
{
    /**
     * @covers \Params\PatchRule\PatchAdd
     * @covers \Params\PatchRule\PatchAdd
     * @covers \Params\PatchRule\PatchCopy
     * @covers \Params\PatchRule\PatchMove
     * @covers \Params\PatchRule\PatchRemove
     * @covers \Params\PatchRule\PatchReplace
     * @covers \Params\PatchRule\PatchTest
     */
    public function testPatchAdd()
    {
        $path = '/sku/prices';
        $class = SkuPriceAdd::class;
        $rules = SkuPriceAdd::getInputParameterList();

        $patchAdd = new PatchAdd($path, $class, $rules);
        $this->assertSame($path, $patchAdd->getPathRegex());
        $this->assertSame($class, $patchAdd->getClassName());
        $this->assertSame($rules, $patchAdd->getRules());
        $this->assertSame("add", $patchAdd->getOpType());

        $patchCopy = new PatchCopy($path, $class, $rules);
        $this->assertSame($path, $patchCopy->getPathRegex());
        $this->assertSame($class, $patchCopy->getClassName());
        $this->assertSame($rules, $patchCopy->getRules());
        $this->assertSame("copy", $patchCopy->getOpType());

        $patchMove = new PatchMove($path, $class, $rules);
        $this->assertSame($path, $patchMove->getPathRegex());
        $this->assertSame($class, $patchMove->getClassName());
        $this->assertSame($rules, $patchMove->getRules());
        $this->assertSame("move", $patchMove->getOpType());

        $patchRemove = new PatchRemove($path, $class, $rules);
        $this->assertSame($path, $patchRemove->getPathRegex());
        $this->assertSame($class, $patchRemove->getClassName());
        $this->assertSame($rules, $patchRemove->getRules());
        $this->assertSame("remove", $patchRemove->getOpType());

        $patchReplace = new PatchReplace($path, $class, $rules);
        $this->assertSame($path, $patchReplace->getPathRegex());
        $this->assertSame($class, $patchReplace->getClassName());
        $this->assertSame($rules, $patchReplace->getRules());
        $this->assertSame("replace", $patchReplace->getOpType());

        $patchTest = new PatchTest($path, $class, $rules);
        $this->assertSame($path, $patchTest->getPathRegex());
        $this->assertSame($class, $patchTest->getClassName());
        $this->assertSame($rules, $patchTest->getRules());
        $this->assertSame("test", $patchTest->getOpType());
    }
}
