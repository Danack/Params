<?php

namespace ParamsTest;

use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 *
 * Allows checking that no code has output characters, or left the output buffer in a bad state.
 *
 */
class BaseTestCase extends TestCase
{
    private $startLevel = null;

    public function setup(): void
    {
        $this->startLevel = ob_get_level();
        ob_start();
    }

    public function teardown(): void
    {
        if ($this->startLevel === null) {
            $this->assertEquals(0, 1, "startLevel was not set, cannot complete teardown");
        }
        $contents = ob_get_contents();
        ob_end_clean();

        $endLevel = ob_get_level();
        $this->assertEquals($endLevel, $this->startLevel, "Mismatched ob_start/ob_end calls....somewhere");
        $this->assertEquals(
            0,
            strlen($contents),
            "Something has directly output to the screen: [".substr($contents, 0, 500)."]"
        );
    }

    public function testPHPUnitApparentlyGetsConfused()
    {
        //Basically despite having:
        //<exclude>*/BaseTestCase.php</exclude>
        //in the phpunit.xml file it still thinks this file is a test class.
        //and then complains about it not having any tests.
        $this->assertTrue(true);
    }


    /**
     * @param string $identifier
     * @param string $problem
     * @param \Params\ValidationProblem[] $validationProblems
     */
    protected function assertValidationProblem(string $identifier, string $expectedProblem, $validationProblems)
    {
        foreach ($validationProblems as $validationProblem) {
            if ($validationProblem->getPath()->toString() !== $identifier) {
                continue;
            }

            if ($validationProblem->getProblemMessage() === $expectedProblem) {
                // correct problem message found
                return;
            }

            $incorrectMessageText = sprintf(
                "Validation problem for identifier '%s' found, but wrong message.\nExpected: '%s'\nActual '%s'\n",
                $identifier,
                $expectedProblem,
                $validationProblem->getProblemMessage()
            );

            $this->fail($incorrectMessageText);
        }

        // TODO - should be path not identifier.
        // Identifier not found
        $identifiers = [];
        foreach ($validationProblems as $validationProblem) {
            $identifiers[] = $validationProblem->getPath()->toString();
        }

        $missingIndentifierText = sprintf(
            "Identifier '%s' not found in validation problems. Identifiers found are '%s'",
            $identifier,
            implode(", ", $identifiers)
        );

        $this->fail($missingIndentifierText);
    }

    /**
     * @param string $identifier
     * @param string $problem
     * @param \Params\ValidationProblem[] $validationProblems
     */
    protected function assertValidationProblemRegexp(string $identifier, string $expectedProblem, $validationProblems)
    {
        $expectedProblemRegexp = stringToRegexp($expectedProblem);

        foreach ($validationProblems as $validationProblem) {
            if ($validationProblem->getPath()->toString() !== $identifier) {
                continue;
            }

            if (preg_match($expectedProblemRegexp, $validationProblem->getProblemMessage())) {
                // correct problem message found
                return;
            }

            $incorrectMessageText = sprintf(
                "Validation problem for identifier '%s' found, but wrong message.\nExpected: '%s'\nActual '%s'\n",
                $identifier,
                $expectedProblem,
                $validationProblem->getProblemMessage()
            );

            $this->fail($incorrectMessageText);
        }

        // Identifier not found
        $pathsAsStrings = [];
        foreach ($validationProblems as $validationProblem) {
            $pathsAsStrings[] = $validationProblem->getPath()->toString();
        }

        $missingIndentifierText = sprintf(
            "Identifier '%s' not found in validation problems. Identifiers found are '%s'",
            $identifier,
            implode(", ", $pathsAsStrings)
        );

        $this->fail($missingIndentifierText);
    }


    /**
     * @param array<array{string, string}> $identifiersAndProblems
     */
    public function assertValidationProblems($identifiersAndProblems, $validationProblems)
    {
        foreach ($identifiersAndProblems as $identifierAndProblem) {
            $identifier = $identifierAndProblem[0];
            $problem = $identifierAndProblem[1];
            $this->assertValidationProblem($identifier, $problem, $validationProblems);
        }
    }


    /**
     * @param string $problem
     * @param \Params\PatchObjectProblem[] $validationProblems
     */
    protected function assertPatchValidationProblem(string $expectedProblem, $validationProblems)
    {
        $messages = [];

        foreach ($validationProblems as $validationProblem) {
            if ($validationProblem->getProblemMessage() === $expectedProblem) {
                // correct problem message found
                return;
            }
            $messages[] = $validationProblem->getProblemMessage();
        }


        $missingIndentifierText = sprintf(
            "Message '%s' not found in PatchValidationProblems. Messages are: [%s]",
            $expectedProblem,
            implode("\n", $messages)
        );

        $this->fail($missingIndentifierText);
    }

    /**
     * @param string[] $problemMessages
     */
    public function assertPatchValidationProblems($problemMessages, $validationProblems)
    {
        foreach ($problemMessages as $problemMessage) {
            $this->assertPatchValidationProblem($problemMessage, $validationProblems);
        }
    }
}
