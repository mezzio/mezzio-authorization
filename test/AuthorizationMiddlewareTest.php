<?php

declare(strict_types=1);

namespace MezzioTest\Authorization;

use Mezzio\Authentication\DefaultUser;
use Mezzio\Authentication\UserInterface;
use Mezzio\Authorization\AuthorizationInterface;
use Mezzio\Authorization\AuthorizationMiddleware;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AuthorizationMiddlewareTest extends TestCase
{
    use ProphecyTrait;

    /** @var AuthorizationInterface|ObjectProphecy */
    private $authorization;

    /** @var ServerRequestInterface|ObjectProphecy */
    private $request;

    /** @var RequestHandlerInterface|ObjectProphecy */
    private $handler;

    /** @var ResponseInterface|ObjectProphecy */
    private $responsePrototype;

    /** @var callable */
    private $responseFactory;

    protected function setUp(): void
    {
        $this->authorization = $this->prophesize(AuthorizationInterface::class);
        $this->request = $this->prophesize(ServerRequestInterface::class);
        $this->handler = $this->prophesize(RequestHandlerInterface::class);
        $this->responsePrototype = $this->prophesize(ResponseInterface::class);
        $this->responseFactory = function () {
            return $this->responsePrototype->reveal();
        };
    }

    public function testConstructor()
    {
        $middleware = new AuthorizationMiddleware($this->authorization->reveal(), $this->responseFactory);
        $this->assertInstanceOf(AuthorizationMiddleware::class, $middleware);
    }

    public function testProcessWithoutUserAttribute()
    {
        $this->request->getAttribute(UserInterface::class, false)->willReturn(false);
        $this->responsePrototype->withStatus(401)->will([$this->responsePrototype, 'reveal']);

        $this->handler
            ->handle(Argument::any())
            ->shouldNotBeCalled();

        $middleware = new AuthorizationMiddleware($this->authorization->reveal(), $this->responseFactory);

        $response = $middleware->process(
            $this->request->reveal(),
            $this->handler->reveal()
        );

        $this->assertSame($this->responsePrototype->reveal(), $response);
    }

    public function testProcessRoleNotGranted()
    {
        $this->request
            ->getAttribute(UserInterface::class, false)
            ->willReturn($this->generateUser('foo', ['bar']));
        $this->responsePrototype
            ->withStatus(403)
            ->will([$this->responsePrototype, 'reveal']);
        $this->authorization
            ->isGranted('bar', Argument::that([$this->request, 'reveal']))
            ->willReturn(false);

        $this->handler
            ->handle(Argument::any())
            ->shouldNotBeCalled();

        $middleware = new AuthorizationMiddleware($this->authorization->reveal(), $this->responseFactory);

        $response = $middleware->process(
            $this->request->reveal(),
            $this->handler->reveal()
        );

        $this->assertSame($this->responsePrototype->reveal(), $response);
    }

    public function testProcessRoleGranted()
    {
        $this->request
            ->getAttribute(UserInterface::class, false)
            ->willReturn($this->generateUser('foo', ['bar']));
        $this->authorization
            ->isGranted('bar', Argument::that([$this->request, 'reveal']))
            ->willReturn(true);

        $this->handler
            ->handle(Argument::any())
            ->will([$this->responsePrototype, 'reveal']);

        $middleware = new AuthorizationMiddleware($this->authorization->reveal(), $this->responseFactory);

        $response = $middleware->process(
            $this->request->reveal(),
            $this->handler->reveal()
        );

        $this->assertSame($this->responsePrototype->reveal(), $response);
    }

    private function generateUser(string $identity, array $roles = []) : DefaultUser
    {
        return new DefaultUser($identity, $roles);
    }
}
