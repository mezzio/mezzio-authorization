<?php

declare(strict_types=1);

namespace MezzioTest\Authorization;

use Mezzio\Authentication\DefaultUser;
use Mezzio\Authentication\UserInterface;
use Mezzio\Authorization\AuthorizationInterface;
use Mezzio\Authorization\AuthorizationMiddleware;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AuthorizationMiddlewareTest extends TestCase
{
    /** @var AuthorizationInterface&MockObject */
    private $authorization;

    /** @var ServerRequestInterface&MockObject */
    private $request;

    /** @var RequestHandlerInterface&MockObject */
    private $handler;

    /** @var ResponseInterface&MockObject */
    private $responsePrototype;

    /** @var callable */
    private $responseFactory;

    protected function setUp(): void
    {
        $this->authorization     = $this->createMock(AuthorizationInterface::class);
        $this->request           = $this->createMock(ServerRequestInterface::class);
        $this->handler           = $this->createMock(RequestHandlerInterface::class);
        $this->responsePrototype = $this->createMock(ResponseInterface::class);
        $this->responseFactory   = function () {
            return $this->responsePrototype;
        };
    }

    public function testProcessWithoutUserAttribute(): void
    {
        $this->request
            ->method('getAttribute')
            ->with(UserInterface::class, false)
            ->willReturnArgument(1);

        $this->responsePrototype
            ->method('withStatus')
            ->with(401)
            ->willReturnSelf();

        $this->handler
            ->expects(self::never())
            ->method('handle');

        $middleware = new AuthorizationMiddleware($this->authorization, $this->responseFactory);

        $response = $middleware->process(
            $this->request,
            $this->handler
        );

        self::assertSame($this->responsePrototype, $response);
    }

    public function testProcessRoleNotGranted(): void
    {
        $this->request
            ->method('getAttribute')
            ->with(UserInterface::class)
            ->willReturn($this->generateUser('foo', ['bar']));

        $this->responsePrototype
            ->method('withStatus')
            ->with(403)
            ->willReturnSelf();

        $this->authorization
            ->method('isGranted')
            ->with('bar', $this->request)
            ->willReturn(false);

        $this->handler
            ->expects(self::never())
            ->method('handle');

        $middleware = new AuthorizationMiddleware($this->authorization, $this->responseFactory);

        $response = $middleware->process(
            $this->request,
            $this->handler
        );

        self::assertSame($this->responsePrototype, $response);
    }

    public function testProcessRoleGranted(): void
    {
        $this->request
            ->method('getAttribute')
            ->with(UserInterface::class)
            ->willReturn($this->generateUser('foo', ['bar']));

        $this->authorization
            ->method('isGranted')
            ->with('bar', $this->request)
            ->willReturn(true);

        $this->handler
            ->expects(self::once())
            ->method('handle')
            ->with($this->request)
            ->willReturn($this->responsePrototype);

        $middleware = new AuthorizationMiddleware($this->authorization, $this->responseFactory);

        $response = $middleware->process(
            $this->request,
            $this->handler
        );

        self::assertSame($this->responsePrototype, $response);
    }

    private function generateUser(string $identity, array $roles = []): DefaultUser
    {
        return new DefaultUser($identity, $roles);
    }
}
