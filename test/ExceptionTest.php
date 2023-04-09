<?php

declare(strict_types=1);

namespace MezzioTest\Authorization;

use Mezzio\Authorization\Exception\ExceptionInterface;
use Mezzio\Authorization\Exception\InvalidConfigException;
use Mezzio\Authorization\Exception\RuntimeException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Throwable;

use function is_a;

class ExceptionTest extends TestCase
{
    /** @return array<array-key, array{0: class-string<Throwable>}> */
    public static function exception(): array
    {
        return [
            [InvalidConfigException::class],
            [RuntimeException::class],
        ];
    }

    #[DataProvider('exception')]
    public function testExceptionIsInstanceOfExceptionInterface(string $exception): void
    {
        self::assertStringContainsString('Exception', $exception);
        self::assertTrue(is_a($exception, ExceptionInterface::class, true));
    }
}
