<?php

declare(strict_types = 1);

namespace ParamsTest\DataLocator;

use Params\DataLocator\DataStorage;
use ParamsTest\BaseTestCase;

class StandardDataLocatorTest extends BaseTestCase
{

//    /**
//     * @group data_locator
//     */
//    public function testPreviousMissingCorrect()
//    {
//        $this->markTestSkipped("Needs fixing");
//
//        $dataLocator = DataStorage::fromArraySetFirstValue([]);
//        [$available, $value] = $dataLocator->getResultByRelativeKey('foo');
//
//        $this->assertFalse($available);
//        $this->assertNull($value);
//    }



//    /**
//     * @group data_locator
//     */
//    public function testPreviousSetCorrect()
//    {
//        $this->markTestSkipped("Needs fixing");
//
//
//        $previousValue = 'bar';
//        $dataLocator = DataStorage::fromArraySetFirstValue([]);
//        $fooDataLocator = $dataLocator->moveKey('foo');
//        $fooDataLocator->storeCurrentResult($previousValue);
//
//        [$available, $value] = $fooDataLocator->getResultByRelativeKey('foo');
//
//        $this->assertTrue($available);
//        $this->assertSame($previousValue, $value);
//
//        $dataLocator->moveKey('foo');
//
//        [$available, $value] = $dataLocator->getResultByRelativeKey('foo');
//
//        $this->assertTrue($available);
//        $this->assertSame($previousValue, $value);
//
//    }

}
