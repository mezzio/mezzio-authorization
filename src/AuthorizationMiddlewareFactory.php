<?php

declare(strict_types=1);

namespace Mezzio\Authorization;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;

use function assert;
use function sprintf;

class AuthorizationMiddlewareFactory
{
    public function __invoke(ContainerInterface $container): AuthorizationMiddleware
    {
        if (
            ! $container->has(AuthorizationInterface::class)
            && ! $container->has('Zend\Expressive\Authorization\AuthorizationInterface')
        ) {
            throw new Exception\InvalidConfigException(sprintf(
                'Cannot create %s service; dependency %s is missing',
                AuthorizationMiddleware::class,
                AuthorizationInterface::class
            ));
        }

        $authorization = $container->has(AuthorizationInterface::class)
            ? $container->get(AuthorizationInterface::class)
            : $container->get('Zend\Expressive\Authorization\AuthorizationInterface');
        assert($authorization instanceof AuthorizationInterface);

        return new AuthorizationMiddleware(
            $authorization,
            $container->get(ResponseInterface::class)
        );
    }
}
