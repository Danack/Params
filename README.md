# Params

A framework agnostic library for validating input parameters.

[![Build Status](https://travis-ci.org/Danack/Params.svg?branch=master)](https://travis-ci.org/Danack/Params)

# Installation

```composer require danack/params```


# TL:DR - Using in an application

This library allows you to define a [set of rules](https://github.com/Danack/Params/blob/1121bda4f5e6a04fcdb4f82a21da0ed83fe79d2f/lib/ParamsExample/GetArticlesParams.php#L71-L92) that define the expected input parameters, and then validate them.

As an example, this is what the code looks like in a controller for retrieving a list of articles:

```
function getArticles(Request $request)
{
    $getArticlesParams = GetArticlesParams::createFromRequest($request);

    echo "After Id: " . $articleGetIndexParams->getAfterId() . PHP_EOL;
    echo "Limit:    " . $articleGetIndexParams->getLimit() . PHP_EOL;
}
```

The above example will throw a `ValidationException` with a list of all the validation problems if there are any.

Alternatively you can have the parameters and list of errors returned as tuple.

```
function getArticles(Request $request)
{
    [$getArticlesParams, $errors] = GetArticlesParams::createOrErrorFromVarMap($request);
    
    if ($errors !== null) {
        // do something about those errors.
    }

    echo "After Id: " . $articleGetIndexParams->getAfterId() . PHP_EOL;
    echo "Limit:    " . $articleGetIndexParams->getLimit() . PHP_EOL;
}
```

# Under the hood, basic usage

Given a set of rules, the library will extract the appropriate values from a 'variable map' and validate that the values meet the defined rules:


```php
$rules = [
  'limit' => [
    new CheckSetOrDefault(10, $variableMap),
    new IntegerInput(),
    new MinIntValue(0),
    new MaxIntValue(100),
  ],
  'offset' => [
    new CheckSetOrDefault(null, $variableMap),
    new SkipIfNull(),
    new MinIntValue(0),
    new MaxIntValue(1000000),
  ],
];

list($limit, $offset) = Params::validate($params);

```

That code will extract the 'limit' and 'offset values from the variable map and check that the limit is an integer between 0 and 100, and that offset is either not set, or must be an integer between 0 and 1,000,000.

If there are any validation problems a ValidationException will be thrown. The validation problems can be retrieved from ValidationException::getValidationProblems.

# Under the hood, basic usage without exceptions

Alternatively, you can avoid using exceptions for flow control:

```php

$validator = new ParamsValidator();

$limit = $validator->validate('limit', [
    new CheckSetOrDefault(10, $variableMap),
    new IntegerInput(),
    new MinIntValue(0),
    new MaxIntValue(100),
]);

$offset = $validator->validate('offset', [
    new CheckSetOrDefault(null, $variableMap),
    new SkipIfNull(),
    new MinIntValue(0),
    new MaxIntValue(1000000),
]);

$errors = $validator->getValidationProblems();

if (count($errors) !== 0) {
    // return an error
    return [null, $errors];
}

// return an object with null 
return [new GetArticlesParams($order, $limit, $offset), null];
```





## Tests

We have several tools that are run to improve code quality. Please run `sh runTests.sh` to run them all. 

Pull requests should have full unit test coverage. Preferably also full mutation coverage through infection.

# Future work


## Parameter location

Some people care whether a parameter is in the query string or body. This library currently doesn't support differentiating them.

