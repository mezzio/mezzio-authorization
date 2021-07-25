<?php

declare(strict_types=1);

namespace Mezzio\Authorization;

use Mezzio\Authentication\UserInterface;
use Mezzio\Authorization\Response\CallableResponseFactoryDecorator;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function is_callable;

class AuthorizationMiddleware implements MiddlewareInterface
{
    /** @var AuthorizationInterface */
    private $authorization;

    /** @var ResponseFactoryInterface */
    private $responseFactory;

    /**
     * @param (callable():ResponseInterface)|ResponseFactoryInterface $responseFactory
     */
    public function __construct(AuthorizationInterface $authorization, $responseFactory)
    {
        $this->authorization = $authorization;

        if (is_callable($responseFactory)) {
            // Ensures type safety of the composed factory
            $responseFactory = new CallableResponseFactoryDecorator(
                static function () use ($responseFactory): ResponseInterface {
                    return $responseFactory();
                }
            );
        }
        $this->responseFactory = $responseFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $user = $request->getAttribute(UserInterface::class, false);
        if (! $user instanceof UserInterface) {
            return $this->responseFactory->createResponse(401);
        }

        foreach ($user->getRoles() as $role) {
            if ($this->authorization->isGranted($role, $request)) {
                return $handler->handle($request);
            }
        }

        return $this->responseFactory->createResponse(403);
    }

    /**
     * @internal This should only be used in unit tests.
     */
    public function getResponseFactory(): ResponseFactoryInterface
    {
        return $this->responseFactory;
    }
}
