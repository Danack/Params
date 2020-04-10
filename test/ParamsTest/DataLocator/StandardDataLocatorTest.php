<?php

declare(strict_types = 1);

namespace ParamsTest\DataLocator;

use Params\DataLocator\DataStorage;
use ParamsTest\BaseTestCase;

/**
 * @covers \Params\DataLocator\DataStorage
 */
class StandardDataLocatorTest extends BaseTestCase
{

    /**
     * @group data_locator
     */
    public function testValueNotAvailable()
    {
        $dataLocator = DataStorage::fromArray([]);
        $dataLocatorAtFoo = $dataLocator->moveKey('foo');

        $available = $dataLocatorAtFoo->valueAvailable();
        $this->assertFalse($available);
    }

    /**
     * @group data_locator
     */
    public function testValueCorrect()
    {
        $this->markTestSkipped("Needs fixing");

        $dataLocator = DataStorage::fromArray(['foo' => 'bar']);
        $dataLocatorAtFoo = $dataLocator->moveKey('foo');

        $available = $dataLocatorAtFoo->valueAvailable();
        $this->assertTrue($available);
        $this->assertSame('bar', $dataLocatorAtFoo->getCurrentValue());
    }
}
