<?php

declare(strict_types = 1);

namespace ParamsTest\Integration;

use VarMap\ArrayVarMap;
use ParamsTest\BaseTestCase;
use Params\ValidationErrors;
use Params\SubsequentRule\DuplicatesParam as DuplicatesParamRule;
use ParamsTest\Integration\DuplicateButWrongTypeParams;

/**
 * @group v2
 * @coversNothing
 */
class DuplicateParamsTest extends BaseTestCase
{
    /**
     * @covers \Params\SubsequentRule\DuplicatesParam
     */
    public function testWorks()
    {
        $password = 'abcde12345';
        $data = [
            'days' => 5,
            'password' => $password,
            'password_repeat' => $password,
        ];

        /** @var DuplicateParams $duplicateParams */
        [$duplicateParams, $error] = DuplicateParams::createOrErrorFromVarMap(
            new ArrayVarMap($data)
        );

        $this->assertNull($error);

        $this->assertInstanceOf(DuplicateParams::class, $duplicateParams);
        $this->assertSame($password, $duplicateParams->getPassword());
        $this->assertSame($password, $duplicateParams->getPasswordRepeat());
    }

    public function providesErrors()
    {
        return [
            [

                [
                    'days' => 6,
                    'password_repeat' => 'zyx12345',
                    DuplicatesParamRule::ERROR_DIFFERENT_TYPES
                ],

                [
                    'password_repeat' => 'zyx12345',
                    DuplicatesParamRule::ERROR_NO_PREVIOUS_PARAM
                ],
            ]
        ];
    }

    /**
     * @covers \Params\SubsequentRule\DuplicatesParam
     */
    public function testDifferentValue()
    {
        $data = [
            'password' => 'abcde12345',
            'password_repeat' => 'zyx12345'
        ];

        /** @var DuplicateParams $duplicateParams */
        [$duplicateParams, $error] = DuplicateParams::createOrErrorFromVarMap(
            new ArrayVarMap($data)
        );

        $this->assertNull($duplicateParams);

        /** @var ValidationErrors $error */
        $validationProblems = $error->getValidationProblems();
        $this->assertCount(1, $validationProblems);

        $this->assertSame(
            "Parameter named 'password_repeat' is different to parameter 'password'.",
            $validationProblems[0]
        );
    }

    /**
     * @covers \Params\SubsequentRule\DuplicatesParam
     */
    public function testMissingPreviousValue()
    {
        $data = [
            'password_repeat' => 'zyx12345'
        ];

        /** @var DuplicateParams $duplicateParams */
        [$duplicateParams, $error] = DuplicateParams::createOrErrorFromVarMap(
            new ArrayVarMap($data)
        );

        $this->assertNull($duplicateParams);

        /** @var ValidationErrors $error */
        $validationProblems = $error->getValidationProblems();
        $this->assertCount(2, $validationProblems);

        $this->assertSame(
            'Value not set for password.',
            $validationProblems[0]
        );


        $this->assertRegExp(
            stringToRegexp(DuplicatesParamRule::ERROR_NO_PREVIOUS_PARAM),
            $validationProblems[1]
        );
    }


    /**
     * @covers \Params\SubsequentRule\DuplicatesParam
     */
    public function testWrongTypePreviousValue()
    {
        $data = [
            'days' => 5,
            'days_repeat' => 'zyx12345'
        ];

        /** @var DuplicateParams $duplicateParams */
        [$duplicateParams, $error] = DuplicateButWrongTypeParams::createOrErrorFromVarMap(
            new ArrayVarMap($data)
        );

        $this->assertNull($duplicateParams);

        /** @var ValidationErrors $error */
        $validationProblems = $error->getValidationProblems();
        $this->assertCount(1, $validationProblems);

        $this->assertRegExp(
            stringToRegexp(DuplicatesParamRule::ERROR_DIFFERENT_TYPES),
            $validationProblems[0]
        );
    }
}
