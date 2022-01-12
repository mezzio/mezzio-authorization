<?php

declare(strict_types=1);

namespace MezzioTest\Authorization;

use Mezzio\Authorization\AuthorizationInterface;
use Mezzio\Authorization\AuthorizationMiddleware;
use Mezzio\Authorization\AuthorizationMiddlewareFactory;
use Mezzio\Authorization\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

class AuthorizationMiddlewareFactoryTest extends TestCase
{
    /** @var ContainerInterface&MockObject */
    private $container;

    /** @var AuthorizationMiddlewareFactory */
    private $factory;

    /** @var AuthorizationInterface&MockObject */
    private $authorization;

    /** @var ResponseInterface&MockObject */
    private $responsePrototype;

    /** @var callable */
    private $responseFactory;

    protected function setUp(): void
    {
        $this->container         = $this->createMock(ContainerInterface::class);
        $this->factory           = new AuthorizationMiddlewareFactory();
        $this->authorization     = $this->createMock(AuthorizationInterface::class);
        $this->responsePrototype = $this->createMock(ResponseInterface::class);
        $this->responseFactory   = function (): ResponseInterface {
            return $this->responsePrototype;
        };

        $this->container
            ->method('get')
            ->withConsecutive(
                [AuthorizationInterface::class],
                [ResponseInterface::class]
            )
            ->willReturnOnConsecutiveCalls($this->authorization, $this->responseFactory);
    }

    public function testFactoryWithoutAuthorization(): void
    {
        $this->expectException(Exception\InvalidConfigException::class);
        ($this->factory)($this->container);
    }

    public function testFactory(): void
    {
        $this->container
            ->method('has')
            ->withConsecutive(
                [AuthorizationInterface::class],
                [ResponseFactoryInterface::class],
                [ResponseInterface::class]
            )
            ->willReturn(true, false, true);

        $middleware = ($this->factory)($this->container);
        $this->assertResponseFactoryReturns($this->responsePrototype, $middleware);
    }

    public static function assertResponseFactoryReturns(
        ResponseInterface $expected,
        AuthorizationMiddleware $middleware
    ): void {
        $responseFactory = $middleware->getResponseFactory();
        self::assertSame($expected, $responseFactory->getResponseFromCallable());
    }
}
