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
    private AuthorizationInterface $authorization;

    /** @var ServerRequestInterface&MockObject */
    private ServerRequestInterface $request;

    /** @var RequestHandlerInterface&MockObject */
    private RequestHandlerInterface $handler;

    /** @var ResponseInterface&MockObject */
    private ResponseInterface $responsePrototype;

    /** @var callable(): ResponseInterface */
    private $responseFactory;

    protected function setUp(): void
    {
        $this->authorization     = $this->createMock(AuthorizationInterface::class);
        $this->request           = $this->createMock(ServerRequestInterface::class);
        $this->handler           = $this->createMock(RequestHandlerInterface::class);
        $this->responsePrototype = $this->createMock(ResponseInterface::class);
        $this->responseFactory   = fn(): ResponseInterface => $this->responsePrototype;
    }

    public function testConstructor(): void
    {
        $middleware = new AuthorizationMiddleware($this->authorization, $this->responseFactory);
        self::assertInstanceOf(AuthorizationMiddleware::class, $middleware);
    }

    public function testProcessWithoutUserAttribute(): void
    {
        $this->request->expects(self::once())
            ->method('getAttribute')
            ->with(UserInterface::class, false)
            ->willReturn(false);

        $this->responsePrototype->expects(self::once())
            ->method('withStatus')
            ->with(401)
            ->willReturn($this->responsePrototype);

        $this->handler->expects(self::never())
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
        $this->request->expects(self::once())
            ->method('getAttribute')
            ->with(UserInterface::class, false)
            ->willReturn($this->generateUser('foo', ['bar']));

        $this->responsePrototype->expects(self::once())
            ->method('withStatus')
            ->with(403)
            ->willReturn($this->responsePrototype);

        $this->authorization->expects(self::once())
            ->method('isGranted')
            ->with('bar', $this->request)
            ->willReturn(false);

        $this->handler->expects(self::never())
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
        $this->request->expects(self::once())
            ->method('getAttribute')
            ->with(UserInterface::class, false)
            ->willReturn($this->generateUser('foo', ['bar']));

        $this->authorization->expects(self::once())
            ->method('isGranted')
            ->with('bar', $this->request)
            ->willReturn(true);

        $this->handler->expects(self::once())
            ->method('handle')
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
