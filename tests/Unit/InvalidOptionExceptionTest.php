<?php

declare(strict_types=1);

namespace Simtabi\Lacommerce\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Simtabi\Lacommerce\Generators\Exceptions\InvalidOptionException;

/**
 * invalidArgument() builds `new static`, so a subclass gets an instance of itself. PHPStan calls that unsafe
 * unless the class promises subclasses keep a compatible constructor, which is what
 * `@phpstan-consistent-constructor` declares. The class stays open so existing subclasses keep working.
 */
final class InvalidOptionExceptionTest extends TestCase
{
    #[Test]
    public function invalid_argument_returns_the_class_it_is_called_on(): void
    {
        $subclass = new class ('') extends InvalidOptionException {
        };

        $exception = $subclass::invalidArgument('bad', 422);

        $this->assertInstanceOf($subclass::class, $exception);
        $this->assertSame('bad', $exception->getMessage());
        $this->assertSame(422, $exception->getCode());
        $this->assertSame(500, InvalidOptionException::invalidArgument('bad')->getCode());
    }

    #[Test]
    public function the_class_stays_extendable_and_declares_a_consistent_constructor(): void
    {
        $class = new ReflectionClass(InvalidOptionException::class);

        $this->assertFalse($class->isFinal());
        $this->assertStringContainsString('@phpstan-consistent-constructor', (string) $class->getDocComment());
    }
}
