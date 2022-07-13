<?php

declare(strict_types=1);

namespace TypeSpecTest\Exception;

use TypeSpecTest\BaseTestCase;
use TypeSpec\Exception\InvalidLocationException;

/**
 * @coversNothing
 */
class InvalidLocationExceptionTest extends BaseTestCase
{
    /**
     * @covers \TypeSpec\Exception\InvalidLocationException
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
     * @covers \TypeSpec\Exception\InvalidLocationException
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
     * @covers \TypeSpec\Exception\InvalidLocationException
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
