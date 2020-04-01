<?php

declare(strict_types = 1);

namespace ParamsTest\Exception\Validator;

use Params\PatchFactory;
use ParamsTest\BaseTestCase;
use Params\PatchValidationResult;
use Params\PatchOperation\TestPatchOperation;
use Params\PatchOperation\RemovePatchOperation;
use Params\PatchOperation\ReplacePatchOperation;
use Params\PatchOperation\MovePatchOperation;
use Params\PatchOperation\CopyPatchOperation;
use Params\PatchOperation\AddPatchOperation;

/**
 * @coversNothing
 * @group patch
 */
class PatchFactoryTest extends BaseTestCase
{

    /**
     * @covers \Params\PatchFactory
     */
    public function testNonArrayData()
    {
        $nonArrayInput = [
            "This is not an array",
        ];

        $result = PatchFactory::convertInputToPatchObjects($nonArrayInput);
        $this->assertTrue($result->isFinalResult());

        $this->assertPatchValidationProblem(
            "Not a valid operation object.",
            $result->getPatchObjectProblems()
        );
    }


        /**
     * @covers \Params\PatchFactory
     */
    public function testBasicErrors()
    {
        $missingPathData = [
            [ "op" => "add" ]
        ];

        $result = PatchFactory::convertInputToPatchObjects($missingPathData);
        $this->assertTrue($result->isFinalResult());

        $problems = $result->getPatchObjectProblems();
        $this->assertCount(1, $problems);
        $this->assertPatchValidationProblem(PatchFactory::$OPERATION_MISSING_PATH, $problems);

        $missingOpData = [
            [ "path" => "/a/b/c" ]
        ];

        $result = PatchFactory::convertInputToPatchObjects($missingOpData);
        $this->assertTrue($result->isFinalResult());
        $problems = $result->getPatchObjectProblems();
        $this->assertCount(1, $problems);
        $this->assertPatchValidationProblem(PatchFactory::$OPERATION_MISSING_OP, $problems);
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

        $this->assertInstanceOf(PatchValidationResult::class, $result);
        $this->assertEmpty(
            $result->getPatchObjectProblems(),
            "Problems were: " . implode($result->getPatchObjectProblems())
        );
        $this->assertFalse($result->isFinalResult());


        $patchOperations = $result->getPatchOperations();
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

        $this->assertInstanceOf(PatchValidationResult::class, $result);
        $this->assertEmpty(
            $result->getPatchObjectProblems(),
            "Problems were: " . implode($result->getPatchObjectProblems())
        );
        $this->assertFalse($result->isFinalResult());


        $patchOperations = $result->getPatchOperations();
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

        $this->assertInstanceOf(PatchValidationResult::class, $result);
        $this->assertEmpty(
            $result->getPatchObjectProblems(),
            "Problems were: " . implode($result->getPatchObjectProblems())
        );
        $this->assertFalse($result->isFinalResult());

        $patchOperations = $result->getPatchOperations();
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
        $data = [
            [ "op" => "remove", "path" => $path]
        ];

        $result = PatchFactory::convertInputToPatchObjects($data);

        $this->assertInstanceOf(PatchValidationResult::class, $result);
        $this->assertEmpty(
            $result->getPatchObjectProblems(),
            "Problems were: " . implode($result->getPatchObjectProblems())
        );
        $this->assertFalse($result->isFinalResult());

        $patchOperations = $result->getPatchOperations();
        $this->assertCount(1, $patchOperations);
        $patchOperation = $patchOperations[0];
        $this->assertInstanceOf(RemovePatchOperation::class, $patchOperation);
        $this->assertSame($path, $patchOperation->getPath());
    }

    /**
     * @covers \Params\PatchFactory
     */
    public function testPatchReplace()
    {
        $path = "/a/b/c";
        $value = "foo";

        $data = [
            [ "op" => "replace", "path" => $path, "value" => $value  ]
        ];

        $result = PatchFactory::convertInputToPatchObjects($data);

        $this->assertInstanceOf(PatchValidationResult::class, $result);
        $this->assertEmpty(
            $result->getPatchObjectProblems(),
            "Problems were: " . implode($result->getPatchObjectProblems())
        );
        $this->assertFalse($result->isFinalResult());

        $patchOperations = $result->getPatchOperations();
        $this->assertCount(1, $patchOperations);
        $patchOperation = $patchOperations[0];
        $this->assertInstanceOf(ReplacePatchOperation::class, $patchOperation);
        $this->assertSame($path, $patchOperation->getPath());
        $this->assertSame($value, $patchOperation->getValue());
    }

    /**
     * @covers \Params\PatchFactory
     */
    public function testPatchTest()
    {
        $value = "foo";
        $path = "/a/b/c";

        $data = [
            [ "op" => "test", "path" => $path, "value" => $value ]
        ];

        $result = PatchFactory::convertInputToPatchObjects($data);

        $this->assertInstanceOf(PatchValidationResult::class, $result);
        $this->assertEmpty(
            $result->getPatchObjectProblems(),
            "Problems were: " . implode($result->getPatchObjectProblems())
        );
        $this->assertFalse($result->isFinalResult());

        $patchOperations = $result->getPatchOperations();
        $this->assertCount(1, $patchOperations);
        $patchOperation = $patchOperations[0];
        $this->assertInstanceOf(TestPatchOperation::class, $patchOperation);
        $this->assertSame($path, $patchOperation->getPath());
        $this->assertSame($value, $patchOperation->getValue());
    }

    /**
     * @covers \Params\PatchFactory
     */
    public function testAddErrorMessage()
    {
        $data = [
            [ "op" => "add", "path" => "/a/b/c"]
        ];
        $result = PatchFactory::convertInputToPatchObjects($data);
        $this->assertInstanceOf(PatchValidationResult::class, $result);
        $this->assertTrue($result->isFinalResult());
        $this->assertTrue($result->anyErrorsFound());
        $this->assertCount(1, $result->getPatchObjectProblems());

        $this->assertPatchValidationProblem(
            "Add operation must contain an entry for 'value'",
            $result->getPatchObjectProblems()
        );
    }

    /**
     * @covers \Params\PatchFactory
     */
    public function testCopyErrorMessage()
    {
        $data = [
            [ "op" => "copy", "path" => "/a/b/c"]
        ];
        $result = PatchFactory::convertInputToPatchObjects($data);
        $this->assertInstanceOf(PatchValidationResult::class, $result);
        $this->assertTrue($result->isFinalResult());
        $this->assertCount(1, $result->getPatchObjectProblems());

        $this->assertPatchValidationProblem(
            "Copy operation must contain an entry for 'from'",
            $result->getPatchObjectProblems()
        );
    }

    /**
     * @covers \Params\PatchFactory
     */
    public function testMoveErrorMessage()
    {
        $data = [
            [ "op" => "move", "path" => "/a/b/c"]
        ];
        $result = PatchFactory::convertInputToPatchObjects($data);
        $this->assertInstanceOf(PatchValidationResult::class, $result);
        $this->assertTrue($result->isFinalResult());
        $this->assertCount(1, $result->getPatchObjectProblems());


        $this->assertPatchValidationProblem(
            "Move operation must contain an entry for 'from'",
            $result->getPatchObjectProblems()
        );
    }

    /**
     * @covers \Params\PatchFactory
     */
    public function testReplaceErrorMessage()
    {
        $data = [
            [ "op" => "replace", "path" => "/a/b/c"]
        ];
        $result = PatchFactory::convertInputToPatchObjects($data);
        $this->assertInstanceOf(PatchValidationResult::class, $result);
        $this->assertTrue($result->isFinalResult());
        $this->assertCount(1, $result->getPatchObjectProblems());

        $this->assertPatchValidationProblem(
            "Replace operation must contain an entry for 'value'",
            $result->getPatchObjectProblems()
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
        $this->assertInstanceOf(PatchValidationResult::class, $result);
        $this->assertTrue($result->isFinalResult());
        $this->assertCount(1, $result->getPatchObjectProblems());

        $this->assertPatchValidationProblem(
            "Test operation must contain an entry for 'value'",
            $result->getPatchObjectProblems()
        );
    }


    /**
     * @covers \Params\PatchFactory
     */
    public function testUnknownErrorMessage()
    {
        $op = 'foobar';

        $data = [
            [ "op" => $op, "path" => "/a/b/c"]
        ];
        $result = PatchFactory::convertInputToPatchObjects($data);
        $this->assertInstanceOf(PatchValidationResult::class, $result);
        $this->assertTrue($result->isFinalResult());
        $this->assertCount(1, $result->getPatchObjectProblems());

        $this->assertPatchValidationProblem(
            "Unknown operation '$op'",
            $result->getPatchObjectProblems()
        );
    }

    /**
     * @covers \Params\PatchFactory
     */
    public function testUnknownTypeErrorMessage()
    {
        $data = [
            'foobar'
        ];
        $result = PatchFactory::convertInputToPatchObjects($data);
        $this->assertInstanceOf(PatchValidationResult::class, $result);
        $this->assertTrue($result->isFinalResult());
        $this->assertCount(1, $result->getPatchObjectProblems());

        $this->assertPatchValidationProblem(
            PatchFactory::$DATA_NOT_ARRAY_FOR_OPERATION,
            $result->getPatchObjectProblems()
        );
    }


    /**
     * @covers \Params\PatchFactory
     */
    public function testInvalidPatchObjects()
    {
        $data = [
            [ "op" => 'test', "path" => "/a/b/c", 'value' => 5], // ok
            [ "op" => 'add'], //missing path
            [ "path" => "/a/b/c"], //missing op
        ];
        $result = PatchFactory::convertInputToPatchObjects($data);
        $this->assertInstanceOf(PatchValidationResult::class, $result);
        $this->assertTrue($result->isFinalResult());

        $patchOperationProblems = $result->getPatchObjectProblems();

        $this->assertCount(2, $patchOperationProblems);
        $missingPath = $patchOperationProblems[0];
        $this->assertSame(1, $missingPath->getOperationIndex());
        $this->assertSame(PatchFactory::$OPERATION_MISSING_PATH, $missingPath->getProblemMessage());

        $missingOp = $patchOperationProblems[1];

        $this->assertSame(2, $missingOp->getOperationIndex());
        $this->assertSame(PatchFactory::$OPERATION_MISSING_OP, $missingOp->getProblemMessage());
    }
}
