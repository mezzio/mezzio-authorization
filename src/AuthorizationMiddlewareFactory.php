<?php

/**
 * @see       https://github.com/mezzio/mezzio-authorization for the canonical source repository
 * @copyright https://github.com/mezzio/mezzio-authorization/blob/master/COPYRIGHT.md
 * @license   https://github.com/mezzio/mezzio-authorization/blob/master/LICENSE.md New BSD License
 */

namespace Mezzio\Authorization;

use Laminas\Diactoros\Response;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;

class AuthorizationMiddlewareFactory
{
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

        if (! $container->has(ResponseInterface::class)
            && ! class_exists(Response::class)
        ) {
            throw new Exception\InvalidConfigException(sprintf(
                'Cannot create %s service; dependency %s is missing. Either define the service, '
                . 'or install laminas/laminas-diactoros',
                AuthorizationMiddleware::class,
                ResponseInterface::class
            ));
        }

        $responsePrototype = $container->has(ResponseInterface::class)
            ? $container->get(ResponseInterface::class)
            : new Response();

        return new AuthorizationMiddleware(
            $container->has(AuthorizationInterface::class) ? $container->get(AuthorizationInterface::class) : $container->get(\Zend\Expressive\Authorization\AuthorizationInterface::class),
            $responsePrototype
        );
    }
}
