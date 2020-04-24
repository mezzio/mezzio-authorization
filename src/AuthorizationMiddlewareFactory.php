<?php

/**
 * @see       https://github.com/mezzio/mezzio-authorization for the canonical source repository
 * @copyright https://github.com/mezzio/mezzio-authorization/blob/master/COPYRIGHT.md
 * @license   https://github.com/mezzio/mezzio-authorization/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Mezzio\Authorization;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;

use function sprintf;

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

        return new AuthorizationMiddleware(
            $container->has(AuthorizationInterface::class)
                ? $container->get(AuthorizationInterface::class)
                : $container->get(\Zend\Expressive\Authorization\AuthorizationInterface::class),
            $container->get(ResponseInterface::class)
        );
    }
}
