<?php

declare(strict_types=1);

namespace Mezzio\Authorization;

use Mezzio\Authentication\UserInterface;
use Mezzio\Authorization\AuthorizationInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AuthorizationMiddleware implements MiddlewareInterface
{
    /** @var callable */
    private $responseFactory;

    public function __construct(private AuthorizationInterface $authorization, callable $responseFactory)
    {
        // Ensures type safety of the composed factory
        $this->responseFactory = static fn(): ResponseInterface => $responseFactory();
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
