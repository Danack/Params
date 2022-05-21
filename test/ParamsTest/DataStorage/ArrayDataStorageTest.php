<?php

declare(strict_types = 1);

namespace ParamsTest\DataStorage;

use Params\DataStorage\ArrayDataStorage;
use Params\DataStorage\TestArrayDataStorage;
use ParamsTest\BaseTestCase;
use Params\Exception\InvalidLocationException;
use function Params\getJsonPointerParts;
use function JsonSafe\json_decode_safe;

/**
 * @covers \Params\DataStorage\ArrayDataStorage
 */
class ArrayDataStorageTest extends BaseTestCase
{
    public function testValueNotAvailable()
    {
        $dataStorage = ArrayDataStorage::fromArray([]);
        $dataStorageAtFoo = $dataStorage->moveKey('foo');

        $available = $dataStorageAtFoo->isValueAvailable();
        $this->assertFalse($available);
    }

    public function testMovingSeparatesPosition()
    {
        $dataStorage = ArrayDataStorage::fromArray([]);
        $dataStorageAtFoo = $dataStorage->moveKey('foo');
        $dataStorageAtFooBar = $dataStorage->moveKey('bar');

        $this->assertSame('/foo', $dataStorageAtFoo->getPath());
        $this->assertSame('/bar', $dataStorageAtFooBar->getPath());
    }

    public function testValueCorrect()
    {
        $dataStorage = ArrayDataStorage::fromArray(['foo' => 'bar']);
        $dataStorageAtFoo = $dataStorage->moveKey('foo');

        $available = $dataStorageAtFoo->isValueAvailable();
        $this->assertTrue($available);
        $this->assertSame('bar', $dataStorageAtFoo->getCurrentValue());
    }


    public function testInvalidLocation()
    {
        $dataStorage = ArrayDataStorage::fromArray(['foo' => 'bar']);
        $dataStorageAtFoo = $dataStorage->moveKey('foo');
        $this->assertTrue($dataStorageAtFoo->isValueAvailable());

        $dataStorageAtJohn = $dataStorage->moveKey('john');
        $this->assertFalse($dataStorageAtJohn->isValueAvailable());

        $this->expectException(InvalidLocationException::class);
        $dataStorageAtJohn->getCurrentValue();
    }

    public function testBadData()
    {
        $dataStorage = TestArrayDataStorage::createMissing('foo');
        $this->assertFalse($dataStorage->isValueAvailable());
    }



    public function providesPathsAreCorrect()
    {
        yield ['/[3]', [3]];
        yield ['/', []];
        yield ['/[0]', [0]];

        yield ['/[0]/foo', [0, 'foo']];
        yield ['/[0]/foo[2]', [0, 'foo', 2]];
        yield ['/foo', ['foo']];
        yield ['/foo[2]', ['foo', 2]];

        yield ['/foo/bar', ['foo', 'bar']];
        yield ['/foo/bar[3]', ['foo', 'bar', 3]];
    }

    /**
     * @dataProvider providesPathsAreCorrect
     */
    public function testPathsAreCorrect($expected, $pathParts)
    {
        $dataStorage = ArrayDataStorage::fromArray([]);

        foreach ($pathParts as $pathPart) {
            $dataStorage = $dataStorage->moveKey($pathPart);
        }

        $this->assertSame($expected, $dataStorage->getPath());
    }

    public static function getTestJson()
    {
        $json = <<< 'JSON'
{
      "foo": ["bar", "baz"],
      "": 0,
      "a/b": 1,
      "c%d": 2,
      "e^f": 3,
      "g|h": 4,
      "i\\j": 5,
      "k\"l": 6,
      " ": 7,
      "m~n": 8
   }
JSON;

        /*

        {
    "foo": ["bar", "baz"],
    "": 0,
    "a/b": 1,
    "c%d": 2,
    "e^f": 3,
    "g|h": 4,
    "i\\j": 5,
    "k\"l": 6,
    " ": 7,
    "m~n": 8

}
        */
        return json_decode_safe($json);
    }



    public function providesJsonPointer()
    {
        yield ['', self::getTestJson()];           // the whole document
        yield ['/foo', ["bar", "baz"]];
        yield ['/foo/0', "bar"];
        yield ['/', 0];
        yield ['/a~1b', 1];
        yield ['/c%d', 2];
        yield ['/e^f', 3];
        yield ['/g|h', 4];

        // TODO - need better code for splitting paths
//        yield ['/i\\j', 5];
//        yield ['/k\"l', 6];
        yield ['/ ', 7];
        yield ['/m~0n', 8];
    }


    /**
     * @dataProvider providesJsonPointer
     */
    public function testJsonPointer($jsonPointer, $expectedData)
    {
        $dataStorage = ArrayDataStorage::fromArray(self::getTestJson());

        $dataStorageAtLocation = $dataStorage->setLocationFromJsonPointer($jsonPointer);

        $this->assertSame($expectedData, $dataStorageAtLocation->getCurrentValue());
    }


    public function providesPathParts()
    {
        // from https://tools.ietf.org/html/rfc6901
        yield ['', []];           // the whole document
        yield ['/foo', ['foo']];
        yield ['/foo/0', ["foo", 0]];
        yield ['/', ['']];
        yield ['/a~1b', ['a/b']];
        yield ['/c%d', ['c%d']];
        yield ['/e^f', ['e^f']];
        yield ['/g|h', ['g|h']];
        yield ['/i\\j', ['i\\j']];
        yield ['/k\"l', ['k\"l']];
        yield ['/ ', [" "]];
        yield ['/m~0n', ['m~n']];

        // other tests
        yield ['/foo/bar', ['foo', 'bar']];
        yield ['/foo/0/bar', ['foo', 0, 'bar']];
        yield ['/foo/bar/0', ['foo', 'bar', 0]];
    }


    /**
     * @dataProvider providesPathParts
     */
    public function testPathParts($jsonPointer, $expectedParts)
    {
        $parts = getJsonPointerParts($jsonPointer);

        $this->assertSame($expectedParts, $parts);
    }
}
