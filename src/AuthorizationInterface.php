<?php

declare(strict_types=1);

namespace Mezzio\Authorization;

use Psr\Http\Message\ServerRequestInterface;

interface AuthorizationInterface
{
    /**
     * Check if a role is granted for the request
     */
    public function isGranted(string $role, ServerRequestInterface $request): bool;
}
