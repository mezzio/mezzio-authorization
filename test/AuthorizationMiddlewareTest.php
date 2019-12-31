<?php

/**
 * @see       https://github.com/mezzio/mezzio-authorization for the canonical source repository
 * @copyright https://github.com/mezzio/mezzio-authorization/blob/master/COPYRIGHT.md
 * @license   https://github.com/mezzio/mezzio-authorization/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace MezzioTest\Authorization;

use Mezzio\Authentication\UserInterface;
use Mezzio\Authentication\UserRepository\UserTrait;
use Mezzio\Authorization\AuthorizationInterface;
use Mezzio\Authorization\AuthorizationMiddleware;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AuthorizationMiddlewareTest extends TestCase
{
    use UserTrait;

    protected function setUp()
    {
        $this->authorization = $this->prophesize(AuthorizationInterface::class);
        $this->request = $this->prophesize(ServerRequestInterface::class);
        $this->handler = $this->prophesize(RequestHandlerInterface::class);
        $this->response = $this->prophesize(ResponseInterface::class);
    }

    public function testConstructor()
    {
        $middleware = new AuthorizationMiddleware($this->authorization->reveal(), $this->response->reveal());
        $this->assertInstanceOf(AuthorizationMiddleware::class, $middleware);
    }

    public function testProcessWithoutUserAttribute()
    {
        $this->request->getAttribute(UserInterface::class, false)->willReturn(false);
        $this->response->withStatus(401)->will([$this->response, 'reveal']);

        $this->handler
            ->handle(Argument::any())
            ->shouldNotBeCalled();

        $middleware = new AuthorizationMiddleware($this->authorization->reveal(), $this->response->reveal());

        $response = $middleware->process(
            $this->request->reveal(),
            $this->handler->reveal()
        );

        $this->assertSame($this->response->reveal(), $response);
    }

    public function testProcessRoleNotGranted()
    {
        $this->request
            ->getAttribute(UserInterface::class, false)
            ->willReturn($this->generateUser('foo', ['bar']));
        $this->response
            ->withStatus(403)
            ->will([$this->response, 'reveal']);
        $this->authorization
            ->isGranted('bar', Argument::that([$this->request, 'reveal']))
            ->willReturn(false);

        $this->handler
            ->handle(Argument::any())
            ->shouldNotBeCalled();

        $middleware = new AuthorizationMiddleware($this->authorization->reveal(), $this->response->reveal());

        $response = $middleware->process(
            $this->request->reveal(),
            $this->handler->reveal()
        );

        $this->assertSame($this->response->reveal(), $response);
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
            ->will([$this->response, 'reveal']);

        $middleware = new AuthorizationMiddleware($this->authorization->reveal(), $this->response->reveal());

        $response = $middleware->process(
            $this->request->reveal(),
            $this->handler->reveal()
        );

        $this->assertSame($this->response->reveal(), $response);
    }
}
