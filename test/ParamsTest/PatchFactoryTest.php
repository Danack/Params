<?php

declare(strict_types = 1);

namespace ParamsTest\Exception\Validator;

use Params\PatchFactory;
use ParamsTest\BaseTestCase;
use Params\ValidationResult;
use Params\PatchOperation\TestPatchOperation;
use Params\PatchOperation\RemovePatchOperation;
use Params\PatchOperation\ReplacePatchOperation;
use Params\PatchOperation\MovePatchOperation;
use Params\PatchOperation\CopyPatchOperation;
use Params\PatchOperation\AddPatchOperation;

/**
 * @coversNothing
 */
class PatchFactoryTest extends BaseTestCase
{
    /**
     * @covers \Params\PatchFactory
     * @group needs_fixing
     */
    public function testBasicErrors()
    {
        $missingPathData = [
            [ "op" => "add",  ]
        ];

        $result = PatchFactory::convertInputToPatchObjects($missingPathData);
        $this->assertTrue($result->isFinalResult());

        $problems = $result->getValidationProblems();
        $this->assertCount(1, $problems);
        $this->assertStringContainsString("missing 'path'", $problems['/']);

        $missingOpData = [
            [ "path" => "/a/b/c",  ]
        ];

        $result = PatchFactory::convertInputToPatchObjects($missingOpData);
        $this->assertTrue($result->isFinalResult());

        $problems = $result->getValidationProblems();
        $this->assertCount(1, $problems);
        $this->assertStringContainsString("missing 'op'", $problems['/']);
    }

    /**
     * @covers \Params\PatchFactory
     */
    public function testPatchAdd()
    {
        $path = "/a/b/c";
        $value = "foo";

        $data = [
            [ "op" => "add", "path" => $path, "value" => $value  ]
        ];

        $result = PatchFactory::convertInputToPatchObjects($data);

        $this->assertInstanceOf(ValidationResult::class, $result);
        $this->assertEmpty(
            $result->getValidationProblems(),
            "Problems were: " . implode($result->getValidationProblems())
        );
        $this->assertFalse($result->isFinalResult());


        $patchOperations = $result->getValue();
        $this->assertCount(1, $patchOperations);
        $patchOperation = $patchOperations[0];
        $this->assertInstanceOf(AddPatchOperation::class, $patchOperation);
        $this->assertSame($path, $patchOperation->getPath());
        $this->assertSame($value, $patchOperation->getValue());
    }

    /**
     * @covers \Params\PatchFactory
     */
    public function testPatchCopy()
    {
        $path = "/a/b/c";
        $from = "/a/b/e";

        $data = [
            [ "op" => "copy", "path" => $path, "from" => $from  ]
        ];

        $result = PatchFactory::convertInputToPatchObjects($data);

        $this->assertInstanceOf(ValidationResult::class, $result);
        $this->assertEmpty(
            $result->getValidationProblems(),
            "Problems were: " . implode($result->getValidationProblems())
        );
        $this->assertFalse($result->isFinalResult());


        $patchOperations = $result->getValue();
        $this->assertCount(1, $patchOperations);
        $patchOperation = $patchOperations[0];
        $this->assertInstanceOf(CopyPatchOperation::class, $patchOperation);
        $this->assertSame($path, $patchOperation->getPath());
        $this->assertSame($from, $patchOperation->getFrom());
    }


    /**
     * @covers \Params\PatchFactory
     */
    public function testPatchMove()
    {
        $path = "/a/b/c";
        $from = "/a/b/e";

        $data = [
            [ "op" => "move", "path" => $path, "from" => $from  ]
        ];

        $result = PatchFactory::convertInputToPatchObjects($data);

        $this->assertInstanceOf(ValidationResult::class, $result);
        $this->assertEmpty(
            $result->getValidationProblems(),
            "Problems were: " . implode($result->getValidationProblems())
        );
        $this->assertFalse($result->isFinalResult());

        $patchOperations = $result->getValue();
        $this->assertCount(1, $patchOperations);
        $patchOperation = $patchOperations[0];
        $this->assertInstanceOf(MovePatchOperation::class, $patchOperation);
        $this->assertSame($path, $patchOperation->getPath());
        $this->assertSame($from, $patchOperation->getFrom());
    }


    /**
     * @covers \Params\PatchFactory
     */
    public function testPatchRemove()
    {
        $path = "/a/b/c";
        $value = "foo";

        $data = [
            [ "op" => "remove", "path" => $path]
        ];

        $result = PatchFactory::convertInputToPatchObjects($data);

        $this->assertInstanceOf(ValidationResult::class, $result);
        $this->assertEmpty(
            $result->getValidationProblems(),
            "Problems were: " . implode($result->getValidationProblems())
        );
        $this->assertFalse($result->isFinalResult());

        $patchOperations = $result->getValue();
        $this->assertCount(1, $patchOperations);
        $patchOperation = $patchOperations[0];
        $this->assertInstanceOf(RemovePatchOperation::class, $patchOperation);
        $this->assertSame($path, $patchOperation->getPath());
    }

    /**
     * @covers \Params\PatchFactory
     * @group needs_fixing
     */
    public function testPatchReplace()
    {
        $path = "/a/b/c";
        $value = "foo";

        $data = [
            [ "op" => "replace", "path" => $path, "value" => $value  ]
        ];

        $result = PatchFactory::convertInputToPatchObjects($data);

        $this->assertInstanceOf(ValidationResult::class, $result);
        $this->assertEmpty(
            $result->getValidationProblems(),
            "Problems were: " . implode($result->getValidationProblems())
        );
        $this->assertFalse($result->isFinalResult());


        $patchOperations = $result->getValue();
        $this->assertCount(1, $patchOperations);
        $patchOperation = $patchOperations[0];
        $this->assertInstanceOf(ReplacePatchOperation::class, $patchOperation);
        $this->assertSame($path, $patchOperation->getPath());
        $this->assertSame($value, $patchOperation->getValue());
    }

    /**
     * @covers \Params\PatchFactory
     * @group needs_fixing
     */
    public function testPatchTest()
    {
        $value = "foo";
        $path = "/a/b/c";

        $data = [
            [ "op" => "test", "path" => $path, "value" => $value ]
        ];

        $result = PatchFactory::convertInputToPatchObjects($data);

        $this->assertInstanceOf(ValidationResult::class, $result);
        $this->assertEmpty(
            $result->getValidationProblems(),
            "Problems were: " . implode($result->getValidationProblems())
        );
        $this->assertFalse($result->isFinalResult());

        $patchOperations = $result->getValue();
        $this->assertCount(1, $patchOperations);
        $patchOperation = $patchOperations[0];
        $this->assertInstanceOf(TestPatchOperation::class, $patchOperation);
        $this->assertSame($path, $patchOperation->getPath());
        $this->assertSame($value, $patchOperation->getValue());
    }



    /**
     * @covers \Params\PatchFactory
     * @group needs_fixing
     */
    public function testAddErrorMessage()
    {
        $data = [
            [ "op" => "add", "path" => "/a/b/c"]
        ];
        $result = PatchFactory::convertInputToPatchObjects($data);
        $this->assertInstanceOf(ValidationResult::class, $result);
        $this->assertTrue($result->isFinalResult());
        $this->assertTrue($result->anyErrorsFound());
        $this->assertCount(1, $result->getValidationProblems());

        $message = $result->getValidationProblems()['/'];
        $this->assertStringContainsString(
            "Add operation must contain an entry for 'value'",
            $message
        );
    }

    /**
     * @covers \Params\PatchFactory
     * @group needs_fixing
     */
    public function testCopyErrorMessage()
    {
        $data = [
            [ "op" => "copy", "path" => "/a/b/c"]
        ];
        $result = PatchFactory::convertInputToPatchObjects($data);
        $this->assertInstanceOf(ValidationResult::class, $result);
        $this->assertTrue($result->isFinalResult());
        $this->assertCount(1, $result->getValidationProblems());

        $message = $result->getValidationProblems()['/'];
        $this->assertStringContainsString(
            "Copy operation must contain an entry for 'from'",
            $message
        );
    }

    /**
     * @covers \Params\PatchFactory
     * @group needs_fixing
     */
    public function testMoveErrorMessage()
    {
        $data = [
            [ "op" => "move", "path" => "/a/b/c"]
        ];
        $result = PatchFactory::convertInputToPatchObjects($data);
        $this->assertInstanceOf(ValidationResult::class, $result);
        $this->assertTrue($result->isFinalResult());
        $this->assertCount(1, $result->getValidationProblems());

        $message = $result->getValidationProblems()['/'];
        $this->assertStringContainsString(
            "Move operation must contain an entry for 'from'",
            $message
        );
    }


    /**
     * @covers \Params\PatchFactory
     * @group needs_fixing
     */
    public function testReplaceErrorMessage()
    {
        $data = [
            [ "op" => "replace", "path" => "/a/b/c"]
        ];
        $result = PatchFactory::convertInputToPatchObjects($data);
        $this->assertInstanceOf(ValidationResult::class, $result);
        $this->assertTrue($result->isFinalResult());
        $this->assertCount(1, $result->getValidationProblems());

        $message = $result->getValidationProblems()['/'];
        $this->assertStringContainsString(
            "Replace operation must contain an entry for 'value'",
            $message
        );
    }

    /**
     * @covers \Params\PatchFactory
     * @group needs_fixing
     */
    public function testTestErrorMessage()
    {
        $data = [
            [ "op" => "test", "path" => "/a/b/c"]
        ];
        $result = PatchFactory::convertInputToPatchObjects($data);
        $this->assertInstanceOf(ValidationResult::class, $result);
        $this->assertTrue($result->isFinalResult());
        $this->assertCount(1, $result->getValidationProblems());

        $message = $result->getValidationProblems()['/'];
        $this->assertStringContainsString(
            "Test operation must contain an entry for 'value'",
            $message
        );
    }


    /**
     * @covers \Params\PatchFactory
     * @group needs_fixing
     */
    public function testUnknownErrorMessage()
    {
        $op = 'foobar';

        $data = [
            [ "op" => $op, "path" => "/a/b/c"]
        ];
        $result = PatchFactory::convertInputToPatchObjects($data);
        $this->assertInstanceOf(ValidationResult::class, $result);
        $this->assertTrue($result->isFinalResult());
        $this->assertCount(1, $result->getValidationProblems());

        $message = $result->getValidationProblems()['/'];
        $this->assertStringContainsString(
            "Unknown operation '$op'",
            $message
        );
    }

    /**
     * @covers \Params\PatchFactory
     * @group needs_fixing
     */
    public function testUnknownTypeErrorMessage()
    {
        $data = [
            'foobar'
        ];
        $result = PatchFactory::convertInputToPatchObjects($data);
        $this->assertInstanceOf(ValidationResult::class, $result);
        $this->assertTrue($result->isFinalResult());
        $this->assertCount(1, $result->getValidationProblems());

        $message = $result->getValidationProblems()['/'];
        $this->assertStringContainsString(
            "is not an array.",
            $message
        );
    }


    /**
     * @covers \Params\PatchFactory
     * @group needs_fixing
     */
    public function testNonArrayErrorMessage()
    {
        $data = [
            'I_am_not_an_array'
        ];
        $result = PatchFactory::convertInputToPatchObjects($data);

        $this->assertInstanceOf(ValidationResult::class, $result);
        $this->assertTrue($result->isFinalResult());
        $this->assertCount(1, $result->getValidationProblems());

        $this->assertCount(1, $result->getValidationProblems());
        $this->markTestSkipped("This needs fixing. Patches should be / based.");
        $this->assertValidationProblem(
            '/',
            "Patch entry 0 is not an array.",
            $result->getValidationProblems()
        );

//        $message = $result->getValidationProblems()['/'];
//        $this->assertStringContainsString(
//            "Patch entry 0 is not an array.",
//            $message
//        );
    }
}
