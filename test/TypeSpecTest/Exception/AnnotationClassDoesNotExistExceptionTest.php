<?php

declare(strict_types=1);

namespace TypeSpecTest\Exception;

use TypeSpec\Messages;
use TypeSpecTest\BaseTestCase;
use TypeSpec\Exception\AnnotationClassDoesNotExistException;

/**
 * @coversNothing
 */
class AnnotationClassDoesNotExistExceptionTest extends BaseTestCase
{
    /**
     * This test seems dumb.
     * @covers \TypeSpec\Exception\AnnotationClassDoesNotExistException
     */
    public function testWorks()
    {
        $exception = AnnotationClassDoesNotExistException::create(
            self::class,
            'property_foo',
            'annotation_bar'
        );

        $expected_message = sprintf(
            Messages::PROPERTY_ANNOTATION_DOES_NOT_EXIST,
            'property_foo',
            self::class,
            'annotation_bar'
        );

        $this->assertSame(
            $expected_message,
            $exception->getMessage()
        );
    }
}
