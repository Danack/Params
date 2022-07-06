<?php

declare(strict_types = 1);

namespace ParamsTest\DataStorage;

use Type\DataStorage\TestArrayDataStorage;
use ParamsTest\BaseTestCase;

/**
 * The class TestArrayDataStorage is only used for testing.
 * This test class mostly just provides coverage.
 *
 * @covers \Type\DataStorage\TestArrayDataStorage
 */
class TestArrayDataStorageTest extends BaseTestCase
{
    public function test_fromSingleValue()
    {
        $dataStorage = TestArrayDataStorage::fromSingleValue('foo', 'bar');

        $this->assertSame('/foo', $dataStorage->getPath());
        $this->assertSame('bar', $dataStorage->getCurrentValue());
    }

    public function test_fromSingleValueButRoot()
    {
        $dataStorage = TestArrayDataStorage::fromSingleValueButRoot('foo', 'bar');
        $this->assertSame('/', $dataStorage->getPath());
    }

    public function test_createMissing()
    {
        $dataStorage = TestArrayDataStorage::createMissing('foo');
        $this->assertSame('/foo', $dataStorage->getPath());
        $this->assertFalse($dataStorage->isValueAvailable());
    }

    public function test_fromArraySetFirstValue()
    {
        $dataStorage = TestArrayDataStorage::fromArraySetFirstValue(['foo' => 'bar']);

        $this->assertSame('/foo', $dataStorage->getPath());
        $this->assertSame('bar', $dataStorage->getCurrentValue());
    }

    public function test_fromArraySetFirstValue_empty()
    {
        $dataStorage = TestArrayDataStorage::fromArraySetFirstValue([]);
        $this->assertSame('/', $dataStorage->getPath());
        $this->assertSame([], $dataStorage->getCurrentValue());
    }
}
