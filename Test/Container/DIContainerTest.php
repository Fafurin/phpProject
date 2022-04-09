<?php

namespace Test\Container;

use App\Container\DIContainer;
use App\Exceptions\NotFoundException;
use App\Repositories\UserRepository;
use App\Repositories\UserRepositoryInterface;
use PHPUnit\Framework\TestCase;


class DIContainerTest extends TestCase
{
    public function testItThrowsAnExceptionIfCannotResolveType(): void
    {
        $container = $this->getDIContainer();

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage(
            'Cannot resolve type: Test\Container\SomeClassWithoutDependencies'
        );

        $container->get(SomeClassWithoutDependencies::class);
    }

    public function testItResolvesClassWithoutDependencies(): void
    {
        $container = $this->getDIContainer();

        $object = $container->get(SomeClassWithoutDependencies::class);

        $this->assertInstanceOf(
            SomeClassWithoutDependencies::class,
            $object
        );
    }

    public function testItResolvesClassByContract(): void
    {
        $container = $this->getDIContainer();

        $container->bind(
            UserRepositoryInterface::class,
            UserRepository::class
        );

        $object = $container->get(UserRepositoryInterface::class);

        $this->assertInstanceOf(
            UserRepository::class,
            $object
        );
    }

    public function testItReturnsPredefinedObject(): void
    {
        $container = $this->getDIContainer();

        $container->bind(
            SomeClassWithParameter::class,
            new SomeClassWithParameter(42)
        );

        $object = $container->get(SomeClassWithParameter::class);

        $this->assertInstanceOf(
            SomeClassWithParameter::class,
            $object
        );

        $this->assertSame(42, $object->value());
    }

    public function testItResolvesClassWithDependencies(): void
    {
        $container = $this->getDIContainer();

        $container->bind(
            SomeClassWithParameter::class,
            new SomeClassWithParameter(42)
        );

        $object = $container->get(ClassDependingOnAnother::class);

        $this->assertInstanceOf(
            ClassDependingOnAnother::class,
            $object
        );
    }

    public function getDIContainer(): DIContainer{
        return DIContainer::getInstance();
    }

}