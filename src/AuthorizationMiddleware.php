<?php

/**
 * @see       https://github.com/mezzio/mezzio-authorization for the canonical source repository
 * @copyright https://github.com/mezzio/mezzio-authorization/blob/master/COPYRIGHT.md
 * @license   https://github.com/mezzio/mezzio-authorization/blob/master/LICENSE.md New BSD License
 */

namespace Mezzio\Authorization;

use Mezzio\Authentication\UserInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Webimpress\HttpMiddlewareCompatibility\HandlerInterface;
use Webimpress\HttpMiddlewareCompatibility\MiddlewareInterface;

use const Webimpress\HttpMiddlewareCompatibility\HANDLER_METHOD;

class AuthorizationMiddleware implements MiddlewareInterface
{
    /**
     * @var AuthorizationInterface
     */
    private $authorization;

    /**
     * @var ResponseInterface
     */
    private $responsePrototype;

    public function __construct(AuthorizationInterface $authorization, ResponseInterface $responsePrototype)
    {
        $this->authorization = $authorization;
        $this->responsePrototype = $responsePrototype;
    }

    /**
     * {@inheritDoc}
     */
    public function process(ServerRequestInterface $request, HandlerInterface $handler)
    {
        $user = $request->getAttribute(UserInterface::class, false);
        if (! $user instanceof UserInterface) {
            return $this->responsePrototype->withStatus(401);
        }

        foreach ($user->getUserRoles() as $role) {
            if ($this->authorization->isGranted($role, $request)) {
                return $handler->{HANDLER_METHOD}($request);
            }
        }
        return $this->responsePrototype->withStatus(403);
    }
}
