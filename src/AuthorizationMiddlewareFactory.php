<?php

/**
 * @see       https://github.com/mezzio/mezzio-authorization for the canonical source repository
 * @copyright https://github.com/mezzio/mezzio-authorization/blob/master/COPYRIGHT.md
 * @license   https://github.com/mezzio/mezzio-authorization/blob/master/LICENSE.md New BSD License
 */

namespace Mezzio\Authorization;

use Laminas\Diactoros\Response;
use Mezzio\Authentication\ResponsePrototypeTrait;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;

class AuthorizationMiddlewareFactory
{
    use ResponsePrototypeTrait;

    public function __invoke(ContainerInterface $container) : AuthorizationMiddleware
    {
        if (! $container->has(AuthorizationInterface::class)
            && ! $container->has(\Zend\Expressive\Authorization\AuthorizationInterface::class)
        ) {
            throw new Exception\InvalidConfigException(sprintf(
                'Cannot create %s service; dependency %s is missing',
                AuthorizationMiddleware::class,
                AuthorizationInterface::class
            ));
        }

        try {
            $responsePrototype = $this->getResponsePrototype($container);
        } catch (\Exception $e) {
            throw new Exception\InvalidConfigException($e->getMessage());
        }

        return new AuthorizationMiddleware(
            $container->has(AuthorizationInterface::class) ? $container->get(AuthorizationInterface::class) : $container->get(\Zend\Expressive\Authorization\AuthorizationInterface::class),
            $responsePrototype
        );
    }
}
