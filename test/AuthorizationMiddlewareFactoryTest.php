<?php

/**
 * @see       https://github.com/mezzio/mezzio-authorization for the canonical source repository
 * @copyright https://github.com/mezzio/mezzio-authorization/blob/master/COPYRIGHT.md
 * @license   https://github.com/mezzio/mezzio-authorization/blob/master/LICENSE.md New BSD License
 */

namespace MezzioTest\Authorization;

use Mezzio\Authorization\AuthorizationInterface;
use Mezzio\Authorization\AuthorizationMiddleware;
use Mezzio\Authorization\AuthorizationMiddlewareFactory;
use Mezzio\Authorization\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;

class AuthorizationMiddlewareFactoryTest extends TestCase
{
    protected function setUp()
    {
        $this->container = $this->prophesize(ContainerInterface::class);
        $this->factory = new AuthorizationMiddlewareFactory();
        $this->authorization = $this->prophesize(AuthorizationInterface::class);
        $this->response = $this->prophesize(ResponseInterface::class);

        $this->container
            ->get(AuthorizationInterface::class)
            ->will([$this->authorization, 'reveal']);
        $this->container
            ->get(ResponseInterface::class)
            ->will([$this->response, 'reveal']);
    }

    public function testFactoryWithoutAuthorization()
    {
        $this->container->has(AuthorizationInterface::class)->willReturn(false);
        $this->container->has(\Zend\Expressive\Authorization\AuthorizationInterface::class)->willReturn(false);

        $this->expectException(Exception\InvalidConfigException::class);
        $middleware = ($this->factory)($this->container->reveal());
    }

    public function testFactoryWithoutResponsePrototype()
    {
        $this->container->has(AuthorizationInterface::class)->willReturn(true);
        $this->container->has(ResponseInterface::class)->willReturn(false);

        $this->expectException(Exception\InvalidConfigException::class);
        $middleware = ($this->factory)($this->container->reveal());
    }

    public function testFactory()
    {
        $this->container->has(AuthorizationInterface::class)->willReturn(true);
        $this->container->has(ResponseInterface::class)->willReturn(true);

        $middleware = ($this->factory)($this->container->reveal());
        $this->assertInstanceOf(AuthorizationMiddleware::class, $middleware);
    }
}
