<?php

declare(strict_types=1);

namespace MezzioTest\Authorization;

use Mezzio\Authorization\AuthorizationMiddleware;
use Mezzio\Authorization\ConfigProvider;
use PHPUnit\Framework\TestCase;

class ConfigProviderTest extends TestCase
{
    /** @var ConfigProvider */
    private $provider;

    protected function setUp(): void
    {
        $this->provider = new ConfigProvider();
    }

    public function testProviderDefinesExpectedFactoryServices(): void
    {
        $config    = $this->provider->getDependencies();
        $factories = $config['factories'];

        $this->assertArrayHasKey(AuthorizationMiddleware::class, $factories);
    }

    public function testInvocationReturnsArrayWithDependencies(): void
    {
        $config = ($this->provider)();

        $this->assertIsArray($config);
        $this->assertArrayHasKey('authorization', $config);
        $this->assertIsArray($config['authorization']);

        $this->assertArrayHasKey('dependencies', $config);
        $this->assertIsArray($config['dependencies']);
        $this->assertArrayHasKey('aliases', $config['dependencies']);
        $this->assertArrayHasKey('factories', $config['dependencies']);
    }
}
