<?php

declare(strict_types=1);

namespace Mezzio\Authorization;

use Mezzio\Authentication\UserInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AuthorizationMiddleware implements MiddlewareInterface
{
    /** @var AuthorizationInterface */
    private $authorization;

    /** @var callable */
    private $responseFactory;

    public function __construct(AuthorizationInterface $authorization, callable $responseFactory)
    {
        $this->authorization = $authorization;

        // Ensures type safety of the composed factory
        $this->responseFactory = function () use ($responseFactory): ResponseInterface {
            return $responseFactory();
        };
    }

    /**
     * {@inheritDoc}
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $user = $request->getAttribute(UserInterface::class, false);
        if (! $user instanceof UserInterface) {
            return ($this->responseFactory)()->withStatus(401);
        }

        foreach ($user->getRoles() as $role) {
            if ($this->authorization->isGranted($role, $request)) {
                return $handler->handle($request);
            }
        }
        return ($this->responseFactory)()->withStatus(403);
    }
}
