<?php

declare(strict_types=1);

namespace ParamsTest\Exception;

use ParamsTest\BaseTestCase;
use Params\Exception\InvalidLocationException;

/**
 * @coversNothing
 */
class InvalidLocationExceptionTest extends BaseTestCase
{
    /**
     * @covers \Params\Exception\InvalidLocationException
     */
    public function testWorksBadArray()
    {
        $location = ['foo', 'bar'];

        $exception = InvalidLocationException::badArrayDataStorage(
            $location
        );

        $this->assertSame(
            $location,
            $exception->getLocation()
        );
        $this->assertStringContainsString(implode(", ", $location), $exception->getMessage());
    }


    /**
     * @covers \Params\Exception\InvalidLocationException
     */
    public function testWorksBadComplex()
    {
        $location = ['foo', 'bar'];

        $exception = InvalidLocationException::badComplexDataStorage(
            $location
        );

        $this->assertSame(
            $location,
            $exception->getLocation()
        );
        $this->assertStringContainsString(implode(", ", $location), $exception->getMessage());
    }


    /**
     * @covers \Params\Exception\InvalidLocationException
     */
    public function testWorksBadInPositionOnObject()
    {
        $location = ['foo', 0];

        $exception = InvalidLocationException::intNotAllowedComplexDataStorage(
            $location
        );

        $this->assertSame(
            $location,
            $exception->getLocation()
        );
        $this->assertStringContainsString(implode(", ", $location), $exception->getMessage());
    }
}
