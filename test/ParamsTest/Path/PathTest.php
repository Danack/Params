<?php

declare(strict_types = 1);

namespace ParamsTest;

use ParamsTest\BaseTestCase;
use Params\Path;
use Params\Path\NameFragment;
use Params\Path\ArrayIndexFragment;

/**
 * @coversNothing
 * @group path
 */
class PathTest extends BaseTestCase
{
    public function testBasicWorks()
    {
        $path = Path::initial();
        $this->assertSame('', $path->toString());
    }

    /**
     * @covers \Params\Path
     * @covers \Params\Path\NameFragment
     */
    public function testNameWorks()
    {
        $path = Path::initial();
        $path2 = $path->addNamePathFragment('john');

        // Test new is correct
        $this->assertSame('john', $path2->toString());

        // Test original unchanged
        $this->assertSame('', $path->toString());
    }

    /**
     * @covers \Params\Path
     * @covers \Params\Path\NameFragment
     * @covers \Params\Path\ArrayIndexFragment
     */
    public function testNameWithArrayWorks()
    {
        $path = Path::initial();
        $path2 = $path->addNamePathFragment('values');
        $path3 = $path2->addArrayIndexPathFragment(2);

        // Test new is correct
        $this->assertSame('values[2]', $path3->toString());
        $this->assertSame('values', $path2->toString());
        $this->assertSame('', $path->toString());
    }


    /**
     * @covers \Params\Path
     * @covers \Params\Path\NameFragment
     * @covers \Params\Path\ArrayIndexFragment
     */
    public function testNameWithArrayThenNameWorks()
    {
        $path = Path::initial();
        $path2 = $path->addNamePathFragment('values');
        $path3 = $path2->addArrayIndexPathFragment(2);
        $path4 = $path3->addNamePathFragment('bar');

        // Test all correct
        $this->assertSame('values[2]/bar', $path4->toString());
        $this->assertSame('values[2]', $path3->toString());
        $this->assertSame('values', $path2->toString());
        $this->assertSame('', $path->toString());
    }
}
