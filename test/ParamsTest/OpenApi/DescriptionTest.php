<?php

declare(strict_types=1);

namespace ParamsTest\OpenApi;

use Params\Rule\GetInt;
use Params\Rule\MaxIntValue;
use ParamsTest\BaseTestCase;
use VarMap\ArrayVarMap;
use Params\ParamsValidator;
use Params\Rule\AlwaysEndsRule;
use ParamsTest\OpenApi\EnumExample;
use ParamsTest\OpenApi\RequiredStringExample;
use Params\OpenApi\StandardParamDescription;
use ParamsTest\OpenApi\RequiredIntExample;


function getParamDescriptionFromRules($allRules)
{
    $ruleDescriptions = [];

    foreach ($allRules as $name => $rules) {
        $description = new StandardParamDescription();

        $description->setName($name);

        foreach ($rules as $rule) {
            /** @var $rule \Params\Rule */
            $rule->updateParamDescription($description);
        }

        $ruleDescriptions[] = $description->toArray();
    }

    return $ruleDescriptions;
}

/**
 * @coversNothing
 */
class DescriptionTest extends BaseTestCase
{
    /**
     * @group wip
     * @covers \Params\OpenApi\StandardParamDescription::generateSchema
     * @covers \Params\OpenApi\StandardParamDescription::setEnum
     */
    public function testEnum()
    {
        $schemaExpectations = [
            'enum' => EnumExample::VALUES,
        ];

        $varMap = new ArrayVarMap([]);
        $rules = EnumExample::getRules($varMap);
        $this->performSchemaTest($schemaExpectations, $rules);
    }

    /**
     * @group wip
     * @covers \Params\OpenApi\StandardParamDescription::generateSchema
     * @covers \Params\OpenApi\StandardParamDescription::setRequired
     */
    public function testRequired()
    {
        $schemaExpectations = [
            'required' => true,
        ];

        $varMap = new ArrayVarMap([]);
        $rules = RequiredStringExample::getRules($varMap);
        $this->performSchemaTest($schemaExpectations, $rules);
    }


    /**
     * @group wip
     * @covers \Params\OpenApi\StandardParamDescription::generateSchema
     * @covers \Params\OpenApi\StandardParamDescription::setMinLength
     */
    public function testMinLength()
    {
        $schemaExpectations = [
            'minLength' => RequiredStringExample::MIN_LENGTH,
        ];

        $varMap = new ArrayVarMap([]);
        $rules = RequiredStringExample::getRules($varMap);
        $this->performSchemaTest($schemaExpectations, $rules);
    }

    /**
     * @group wip
     * @covers \Params\OpenApi\StandardParamDescription::generateSchema
     * @covers \Params\OpenApi\StandardParamDescription::setMinLength
     */
    public function testMaxLength()
    {
        $schemaExpectations = [
            'maxLength' => RequiredStringExample::MAX_LENGTH,
        ];

        $varMap = new ArrayVarMap([]);
        $rules = RequiredStringExample::getRules($varMap);
        $this->performSchemaTest($schemaExpectations, $rules);
    }





    /**
     * @group wip
     * @covers \Params\OpenApi\StandardParamDescription::generateSchema
     * @covers \Params\OpenApi\StandardParamDescription::setMinLength
     */
    public function testMinInt()
    {
        $schemaExpectations = [
            'minimum' => RequiredIntExample::MIN,
        ];

        $varMap = new ArrayVarMap([]);
        $rules = RequiredIntExample::getRules($varMap);
        $this->performSchemaTest($schemaExpectations, $rules);
    }

    /**
     * @group wip
     * @covers \Params\OpenApi\StandardParamDescription::generateSchema
     * @covers \Params\OpenApi\StandardParamDescription::setMinLength
     */
    public function testMaxInt()
    {
        $schemaExpectations = [
            'maximum' => RequiredIntExample::MAX,
        ];

        $varMap = new ArrayVarMap([]);
        $rules = RequiredIntExample::getRules($varMap);
        $this->performSchemaTest($schemaExpectations, $rules);
    }


    private function performSchemaTest($schemaExpectations, $rules)
    {
        $paramDescription = getParamDescriptionFromRules($rules);

        $this->assertCount(1, $paramDescription);
        $statusDescription = $paramDescription[0];

        $this->assertArrayHasKey('schema', $statusDescription);
        $schema = $statusDescription['schema'];

        foreach ($schemaExpectations as $key => $value) {
            $this->assertArrayHasKey($key, $schema, "Schema missing key [$key]. Schema is " .json_encode($schema));
            $this->assertEquals($value, $schema[$key]);
        }
    }
}
