<?php

declare(strict_types=1);

namespace Mezzio\Authorization;

use Psr\Container\ContainerInterface;

use function sprintf;

class AuthorizationMiddlewareFactory
{
    use Psr17ResponseFactoryTrait;

    public function __invoke(ContainerInterface $container): AuthorizationMiddleware
    {
        $hasAuthorization           = $container->has(AuthorizationInterface::class);
        $hasDeprecatedAuthorization = false;
        if (! $hasAuthorization) {
            $hasDeprecatedAuthorization = $container->has(\Zend\Expressive\Authorization\AuthorizationInterface::class);
        }
        if (
            ! $hasAuthorization
            && ! $hasDeprecatedAuthorization
        ) {
            throw new Exception\InvalidConfigException(sprintf(
                'Cannot create %s service; dependency %s is missing',
                AuthorizationMiddleware::class,
                AuthorizationInterface::class
            ));
        }

        return new AuthorizationMiddleware(
            $hasAuthorization
                ? $container->get(AuthorizationInterface::class)
                : $container->get(\Zend\Expressive\Authorization\AuthorizationInterface::class),
            $this->detectResponseFactory($container)
        );
    }
}
