<p align="center">
    <a href="https://github.com/yiisoft" target="_blank">
        <img src="https://yiisoft.github.io/docs/images/yii_logo.svg" height="100px">
    </a>
    <h1 align="center">Yii Definitions</h1>
    <br>
</p>

[![Latest Stable Version](https://poser.pugx.org/yiisoft/definitions/v/stable.png)](https://packagist.org/packages/yiisoft/definitions)
[![Total Downloads](https://poser.pugx.org/yiisoft/definitions/downloads.png)](https://packagist.org/packages/yiisoft/definitions)
[![Build status](https://github.com/yiisoft/definitions/workflows/build/badge.svg)](https://github.com/yiisoft/definitions/actions?query=workflow%3Abuild)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/yiisoft/definitions/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/yiisoft/definitions/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/yiisoft/definitions/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/yiisoft/definitions/?branch=master)
[![Mutation testing badge](https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fyiisoft%2Fdefinitions%2Fmaster)](https://dashboard.stryker-mutator.io/reports/github.com/yiisoft/definitions/master)
[![static analysis](https://github.com/yiisoft/definitions/workflows/static%20analysis/badge.svg)](https://github.com/yiisoft/definitions/actions?query=workflow%3A%22static+analysis%22)
[![type-coverage](https://shepherd.dev/github/yiisoft/definitions/coverage.svg)](https://shepherd.dev/github/yiisoft/definitions)

The package provides syntax constructs describing a way to create and configure a service or an object. It is used by [yiisoft/di](https://github.com/yiisoft/di) and [yiisoft/factory](https://github.com/yiisoft/factory)
but could be used in other [PSR-11](https://www.php-fig.org/psr/psr-11/) compatible packages as well.

The following are provided:

- Definitions describing services or objects to create. This includes syntax, its validation and resolving it to objects.
- References and dynamic references to point to other definitions. These include additional utility to refer to multiple 
  definitions at once.

## Requirements

- PHP 7.4 or higher.

## Installation

The package could be installed with composer:

```shell
composer require yiisoft/definitions --prefer-dist
```

## General usage

### Definitions

Definition is describing a way to create and configure a service, an object
or return any other value. It must implement `Yiisoft\Definitions\Contract\DefinitionInterface`
that has a single method `resolve(ContainerInterface $container)`. References are
typically stored in the container or a factory and are resolved into object
at the moment of obtaining a service instance or creating an object.

#### `ClassDefinition`

Class definition points to a class or interface name. Union type could be used as well.

```php
use \Yiisoft\Definitions\ClassDefinition;

$definition = new ClassDefinition(MyServiceInterface::class, false);
$object = $definition->resolve($container);
```

The second argument above is an "optional" flag. Set it to true if null should be returned instead of throwing
an exception when resolving the definition.

#### `ArrayDefinition`

Array definition allows describing a service or an object declaratively:

```php
use \Yiisoft\Definitions\ArrayDefinition;

$definition = ArrayDefinition::fromConfig([
    'class' => MyServiceInterface::class,
    '__construct()' => [42], 
    '$propertyName' => 'value',
    'setName()' => ['Alex'],
]);
$object = $definition->resolve($container);
```

In the above:

- `class` contains the name of the class to be instantiated.
- `__construct()` holds an array of constructor arguments.
- The rest of the config are property values (prefixed with `$`)
  and method calls, postfixed with `()`. They are set/called
  in the order they appear in the array.

#### `CallableDefinition`

Callable definition builds an object by executing a callable injecting
dependencies based on types used in its signature:

```php
use \Yiisoft\Definitions\CallableDefinition;

$definition = new CallableDefinition(
    fn (SomeFactory $factory) => $factory->create('args')
);
$object = $definition->resolve($container);

// or 

$definition = new CallableDefinition(
    fn () => MyFactory::create('args')
);
$object = $definition->resolve($container);

// or

$definition = new CallableDefinition(
    [MyFactory::class, 'create']
);
$object = $definition->resolve($container);
```

In the above we use a closure, a static call and a static
method passed as array-callable. In each case we determine
and pass dependencies based on the types of arguments in
the callable signature.

#### `ParameterDefinition`

Parameter definition resolves an object based on information from `ReflectionParameter` instance:

```php
use \Yiisoft\Definitions\ParameterDefinition;

$definition = new ParameterDefinition($reflectionParameter);
$object = $definition->resolve($container);
```

It is mostly used internally when working with callables.

#### `ValueDefinition`

Value definition resolves value passed as is:

```php
use \Yiisoft\Definitions\ValueDefinition;

$definition = new ValueDefinition(42, 'int');
$value = $definition->resolve($container); // 42
```

### References

References point to other definitions so when defining a definition you can use other definitions as its
dependencies:

```php
[
    InterfaceA::class => ConcreteA::class,
    'alternativeForA' => ConcreteB::class,
    MyService::class => [
        '__construct()' => [
            Reference::to('alternativeForA'),
        ],
    ],
]
```

The `DynamicReference` defines a dependency to a service not defined in the container:

```php
[
   MyService::class => [
       '__construct()' => [
           DynamicReference::to([
               'class' => SomeClass::class,
               '$someProp' => 15
           ])
       ]
   ]
]
```

## Testing

### Unit testing

The package is tested with [PHPUnit](https://phpunit.de/). To run tests:

```shell
./vendor/bin/phpunit
```

### Mutation testing

The package tests are checked with [Infection](https://infection.github.io/) mutation framework with
[Infection Static Analysis Plugin](https://github.com/Roave/infection-static-analysis-plugin). To run it:

```shell
./vendor/bin/roave-infection-static-analysis-plugin
```

### Static analysis

The code is statically analyzed with [Psalm](https://psalm.dev/). To run static analysis:

```shell
./vendor/bin/psalm
```

## License

The Yii Definitions is free software. It is released under the terms of the BSD License.
Please see [`LICENSE`](./LICENSE.md) for more information.

Maintained by [Yii Software](https://www.yiiframework.com/).

## Support the project

[![Open Collective](https://img.shields.io/badge/Open%20Collective-sponsor-7eadf1?logo=open%20collective&logoColor=7eadf1&labelColor=555555)](https://opencollective.com/yiisoft)

## Follow updates

[![Official website](https://img.shields.io/badge/Powered_by-Yii_Framework-green.svg?style=flat)](https://www.yiiframework.com/)
[![Twitter](https://img.shields.io/badge/twitter-follow-1DA1F2?logo=twitter&logoColor=1DA1F2&labelColor=555555?style=flat)](https://twitter.com/yiiframework)
[![Telegram](https://img.shields.io/badge/telegram-join-1DA1F2?style=flat&logo=telegram)](https://t.me/yii3en)
[![Facebook](https://img.shields.io/badge/facebook-join-1DA1F2?style=flat&logo=facebook&logoColor=ffffff)](https://www.facebook.com/groups/yiitalk)
[![Slack](https://img.shields.io/badge/slack-join-1DA1F2?style=flat&logo=slack)](https://yiiframework.com/go/slack)
