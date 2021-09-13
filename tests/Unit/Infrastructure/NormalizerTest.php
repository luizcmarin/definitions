<?php

declare(strict_types=1);

namespace Yiisoft\Definitions\Tests\Unit\Infrastructure;

use PHPUnit\Framework\TestCase;
use stdClass;
use Yiisoft\Definitions\ArrayDefinition;
use Yiisoft\Definitions\Exception\InvalidConfigException;
use Yiisoft\Definitions\Infrastructure\Normalizer;
use Yiisoft\Definitions\Reference;
use Yiisoft\Definitions\Tests\Objects\ColorPink;
use Yiisoft\Definitions\Tests\Objects\GearBox;
use Yiisoft\Definitions\Tests\Support\SimpleDependencyResolver;
use Yiisoft\Definitions\ValueDefinition;

final class NormalizerTest extends TestCase
{
    public function testReference(): void
    {
        $reference = Reference::to('test');

        $this->assertSame($reference, Normalizer::normalize($reference));
    }

    public function testClass(): void
    {
        /** @var ArrayDefinition $definition */
        $definition = Normalizer::normalize(ColorPink::class);

        $this->assertInstanceOf(ArrayDefinition::class, $definition);
        $this->assertSame(ColorPink::class, $definition->getClass());
        $this->assertSame([], $definition->getConstructorArguments());
        $this->assertSame([], $definition->getMethodsAndProperties());
    }

    public function testArray(): void
    {
        /** @var ArrayDefinition $definition */
        $definition = Normalizer::normalize(
            [
                '__construct()' => [42],
            ],
            GearBox::class
        );

        $this->assertInstanceOf(ArrayDefinition::class, $definition);
        $this->assertSame(GearBox::class, $definition->getClass());
        $this->assertSame([42], $definition->getConstructorArguments());
        $this->assertSame([], $definition->getMethodsAndProperties());
    }

    public function testReadyObject(): void
    {
        $dependencyResolver = new SimpleDependencyResolver();

        $object = new stdClass();

        /** @var ValueDefinition $definition */
        $definition = Normalizer::normalize($object);

        $this->assertInstanceOf(ValueDefinition::class, $definition);
        $this->assertSame($object, $definition->resolve($dependencyResolver));
    }

    public function testInteger(): void
    {
        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage('Invalid definition: 42');
        Normalizer::normalize(42);
    }
}