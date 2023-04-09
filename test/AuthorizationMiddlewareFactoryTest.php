<?php

declare(strict_types=1);

namespace MezzioTest\Authorization;

use Mezzio\Authorization\AuthorizationInterface;
use Mezzio\Authorization\AuthorizationMiddleware;
use Mezzio\Authorization\AuthorizationMiddlewareFactory;
use Mezzio\Authorization\Exception;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use ReflectionProperty;

class AuthorizationMiddlewareFactoryTest extends TestCase
{
    /** @var ContainerInterface&MockObject */
    private ContainerInterface $container;

    private AuthorizationMiddlewareFactory $factory;

    /** @var AuthorizationInterface&MockObject */
    private AuthorizationInterface $authorization;

    /** @var ResponseInterface&MockObject */
    private ResponseInterface $responsePrototype;

    /** @var callable(): ResponseInterface */
    private $responseFactory;

    protected function setUp(): void
    {
        $this->container         = $this->createMock(ContainerInterface::class);
        $this->factory           = new AuthorizationMiddlewareFactory();
        $this->authorization     = $this->createMock(AuthorizationInterface::class);
        $this->responsePrototype = $this->createMock(ResponseInterface::class);
        $this->responseFactory   = fn(): ResponseInterface => $this->responsePrototype;

        $this->container
            ->method('get')
            ->willReturnMap([
                [AuthorizationInterface::class, $this->authorization],
                [ResponseInterface::class, $this->responseFactory],
            ]);
    }

    public function testFactoryWithoutAuthorization(): void
    {
        $this->container->expects(self::exactly(2))
            ->method('has')
            ->willReturnMap([
                [AuthorizationInterface::class, false],
                ['Zend\Expressive\Authorization\AuthorizationInterface', false],
            ]);
        $this->expectException(Exception\InvalidConfigException::class);
        ($this->factory)($this->container);
    }

    public function testFactory(): void
    {
        $this->container->expects(self::exactly(2))
            ->method('has')
            ->willReturnMap([
                [AuthorizationInterface::class, true],
                [ResponseInterface::class, true],
            ]);

        $middleware = ($this->factory)($this->container);
        self::assertInstanceOf(AuthorizationMiddleware::class, $middleware);
        self::assertResponseFactoryReturns($this->responsePrototype, $middleware);
    }

    public static function assertResponseFactoryReturns(
        ResponseInterface $expected,
        AuthorizationMiddleware $middleware
    ): void {
        $r               = new ReflectionProperty($middleware, 'responseFactory');
        $responseFactory = $r->getValue($middleware);
        Assert::assertSame($expected, $responseFactory());
    }
}
