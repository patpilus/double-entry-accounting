<?php

namespace App\Core\Domain\Amount;

use App\Core\Domain\Amount as AmountInterface;
use PHPUnit\Framework\TestCase;

class AmountTest extends TestCase
{
    public function testCanBeCreatedFromString(): void
    {
        $amount = Amount::fromString('1444.22');

        self::assertInstanceOf(AmountInterface::class, $amount);
        self::assertSame('1444.22', $amount->value());
    }

    public function testCanBeCreatedWithNegativeValue(): void
    {
        $amount = Amount::fromString('-100');

        self::assertSame('-100', $amount->value());
    }

    public function testAllowToMathematicallyRoundTheValue(): void
    {
        $amount = Amount::fromString('-100');
        self::assertSame('-100', $amount->mathRound(0)->value());
        self::assertSame('-100.00', $amount->mathRound(2)->value());

        $amount = Amount::fromString('1524.9754802');
        self::assertSame('1524.975480', $amount->mathRound(6)->value());
        self::assertSame('1524.97548', $amount->mathRound(5)->value());
        self::assertSame('1524.9755', $amount->mathRound(4)->value());
        self::assertSame('1524.975', $amount->mathRound(3)->value());
        self::assertSame('1524.98', $amount->mathRound(2)->value());
        self::assertSame('1525.0', $amount->mathRound(1)->value());
        self::assertSame('1525', $amount->mathRound(0)->value());
    }

    public function testAllowsToPerformAdditionOperation(): void
    {
        $amount = Amount::fromFloat(0);
        $amount = $amount->add(Amount::fromString('-100'));

        self::assertSame('-100.00', $amount->value());
    }

    public function testAllowsToPerformSubtractionOperation(): void
    {
        $amount = Amount::fromFloat('1432.12');
        $amount = $amount->subtract(Amount::fromString('1432.15'));

        self::assertSame('-0.03', $amount->value());
    }

    public function testAllowsToCheckWhetherTheValueIsLessThanGivenOne(): void
    {
        $amount = Amount::fromString('1524.9755');

        self::assertFalse($amount->isLessThan(Amount::fromString('1524.9755'), 4));
        self::assertTrue($amount->isLessThan(Amount::fromString('1524.9756'), 4));
        self::assertTrue($amount->isLessThan(Amount::fromString('8457694'), 4));
    }

    public function testAllowsToCheckWhetherTheValueIsEqualWithGivenOne(): void
    {
        $amount = Amount::fromString('1524.9755');

        self::assertTrue($amount->isEqualWith(Amount::fromString('1524.9755'), 4));
        self::assertTrue($amount->isEqualWith(Amount::fromString('1524.98'), 2));
        self::assertFalse($amount->isEqualWith(Amount::fromString('1524.9756'), 4));
        self::assertFalse($amount->isEqualWith(Amount::fromString('8457694'), 4));
    }

    public function testAllowsToCheckWhetherTheValueIsGreaterThanGivenOne(): void
    {
        $amount = Amount::fromString('1524.9755');

        self::assertFalse($amount->isGreaterThan(Amount::fromString('1524.9755'), 4));
        self::assertTrue($amount->isGreaterThan(Amount::fromString('1524.9754'), 4));
        self::assertFalse($amount->isGreaterThan(Amount::fromString('8457694'), 4));
    }
}
