<?php

declare(strict_types=1);

namespace ParamsTest\Exception;

use ParamsTest\BaseTestCase;
use Type\Exception\InvalidLocationException;

/**
 * @coversNothing
 */
class InvalidLocationExceptionTest extends BaseTestCase
{
    /**
     * @covers \Type\Exception\InvalidLocationException
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
     * @covers \Type\Exception\InvalidLocationException
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
     * @covers \Type\Exception\InvalidLocationException
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
