<?php

declare(strict_types = 1);

namespace ParamsTest\DataStorage;

use Params\DataStorage\ComplexDataStorage;
use Params\DataStorage\TestArrayDataStorage;
use ParamsTest\BaseTestCase;
use Params\Exception\InvalidLocationException;
use function Params\getJsonPointerParts;
use function JsonSafe\json_decode_safe;

/**
 * @covers \Params\DataStorage\ComplexDataStorage
 */
class ComplexDataStorageTest extends BaseTestCase
{
    public function testValueNotAvailable()
    {
        $dataStorage = ComplexDataStorage::fromData([]);
        $dataStorageAtFoo = $dataStorage->moveKey('foo');

        $available = $dataStorageAtFoo->isValueAvailable();
        $this->assertFalse($available);
    }

    public function testValueNotAvailableGettingErrors()
    {
        $data = new \StdClass();

        $dataStorage = ComplexDataStorage::fromData($data);
        $dataStorageAtFoo = $dataStorage->moveKey('foo');

        $this->expectException(InvalidLocationException::class);
        $dataStorageAtFoo->getCurrentValue();
    }

    public function testValueNotAvailableAsScalar()
    {
        $data = new \StdClass();
        $data->color = 'red';

        $dataStorage = ComplexDataStorage::fromData($data);
        $dataStorageAtColor = $dataStorage->moveKey('color');
        $dataStorageAtBadPosition = $dataStorageAtColor->moveKey('bar');

        $this->expectException(InvalidLocationException::class);
        $dataStorageAtBadPosition->isValueAvailable();
    }


    public function testErrorIntPositionOnObject_isValueAvailable()
    {
        $dataStorage = ComplexDataStorage::fromData(new \StdClass());
        $dataStorageAtFoo = $dataStorage->moveKey(0);

        $this->expectException(InvalidLocationException::class);
        $dataStorageAtFoo->isValueAvailable();
    }

    public function testErrorIntPositionOnObject_getCurrentValue()
    {
        $dataStorage = ComplexDataStorage::fromData(new \StdClass());
        $dataStorageAtFoo = $dataStorage->moveKey(0);

        $this->expectException(InvalidLocationException::class);
        $dataStorageAtFoo->getCurrentValue();
    }

    public function testGettingValueNotAvailableAsScalar()
    {
        $data = new \StdClass();
        $data->color = 'red';

        $dataStorage = ComplexDataStorage::fromData($data);
        $dataStorageAtColor = $dataStorage->moveKey('color');
        $dataStorageAtBadPosition = $dataStorageAtColor->moveKey('bar');

        $this->expectException(InvalidLocationException::class);
        $dataStorageAtBadPosition->getCurrentValue();
    }


    public function testMovingSeparatesPosition()
    {
        $dataStorage = ComplexDataStorage::fromData([]);
        $dataStorageAtFoo = $dataStorage->moveKey('foo');
        $dataStorageAtFooBar = $dataStorage->moveKey('bar');

        $this->assertSame('/foo', $dataStorageAtFoo->getPath());
        $this->assertSame('/bar', $dataStorageAtFooBar->getPath());
    }

    public function testValueCorrectAsArray()
    {
        $dataStorage = ComplexDataStorage::fromData(['foo' => 'bar']);
        $dataStorageAtFoo = $dataStorage->moveKey('foo');

        $available = $dataStorageAtFoo->isValueAvailable();
        $this->assertTrue($available);
        $this->assertSame('bar', $dataStorageAtFoo->getCurrentValue());
    }


    public function testValueCorrectAsObject()
    {
        $data = new \StdClass();
        $data->foo = 'bar';

        $dataStorage = ComplexDataStorage::fromData($data);
        $dataStorageAtFoo = $dataStorage->moveKey('foo');

        $available = $dataStorageAtFoo->isValueAvailable();
        $this->assertTrue($available);
        $this->assertSame('bar', $dataStorageAtFoo->getCurrentValue());
    }


    public function testValueCorrectlyNotAvailableAsObject()
    {
        $data = new \StdClass();
        $data->foo = 'bar';

        $dataStorage = ComplexDataStorage::fromData($data);
        $dataStorageAtFoo = $dataStorage->moveKey('doesnotexist');

        $this->assertFalse($dataStorageAtFoo->isValueAvailable());
    }



    public function testValueCorrect_advanced1()
    {
        $obj = new \StdClass();

        $obj1 = new \StdClass();
        $obj1->color = 'red';

        $obj2 = new \StdClass();
        $obj2->color = 'blue';

        $objColors = [$obj1, $obj2];
        $subData =  [1, 2, 3, $objColors];
        $obj->values = $subData;

        $dataStorage = ComplexDataStorage::fromData($obj);
        $dataStorageAtValues = $dataStorage->moveKey('values');

        $available = $dataStorageAtValues->isValueAvailable();
        $this->assertTrue($available);
        $this->assertSame($subData, $dataStorageAtValues->getCurrentValue());

        $dataStorageAtValues_0 = $dataStorageAtValues->moveKey(0);
        $this->assertTrue($dataStorageAtValues_0->isValueAvailable());
        $this->assertSame(1, $dataStorageAtValues_0->getCurrentValue());

        $dataStorageAtValues_3 = $dataStorageAtValues->moveKey(3);
        $this->assertTrue($dataStorageAtValues_3->isValueAvailable());
        $this->assertSame($objColors, $dataStorageAtValues_3->getCurrentValue());
    }




    public function testInvalidLocation()
    {
        $dataStorage = ComplexDataStorage::fromData(['foo' => 'bar']);
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
        $dataStorage = ComplexDataStorage::fromData([]);

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
        $dataStorage = ComplexDataStorage::fromData(self::getTestJson());

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
