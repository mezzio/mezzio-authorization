<?php

/**
 * @see       https://github.com/mezzio/mezzio-authorization for the canonical source repository
 * @copyright https://github.com/mezzio/mezzio-authorization/blob/master/COPYRIGHT.md
 * @license   https://github.com/mezzio/mezzio-authorization/blob/master/LICENSE.md New BSD License
 */

namespace Mezzio\Authorization;

use Laminas\Permission\Rbac\Rbac;

class ConfigProvider
{
    /**
     * Return the configuration array.
     */
    public function __invoke() : array
    {
        return [
            'dependencies'  => $this->getDependencies(),
            'authorization' => include __DIR__ . '/../config/authorization.php'
        ];
    }

    /**
     * Returns the container dependencies
     */
    public function getDependencies() : array
    {
        return [
            // Legacy Zend Framework aliases
            'aliases' => [
                \Zend\Expressive\Authorization\AuthorizationMiddleware::class => AuthorizationMiddleware::class,
            ],
            'factories'  => [
                AuthorizationMiddleware::class => AuthorizationMiddlewareFactory::class
            ],
        ];
    }
}
