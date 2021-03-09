<?php

declare(strict_types=1);

namespace App\Core\Domain\Amount;

use App\Core\Domain\Amount as AmountInterface;

final class Amount extends AbstractAmount
{
    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public static function fromFloat(float $value): self
    {
        return new self((string) $value);
    }

    public function add(AmountInterface $amount, int $scale = 2): AmountInterface
    {
        $this->guardNotNegativeScale($scale);

        return (new self(bcadd($this->value, (string) $amount, $scale + 1)))->mathRound($scale);
    }

    public function subtract(AmountInterface $amount, int $scale = 2): AmountInterface
    {
        $this->guardNotNegativeScale($scale);

        return (new self(bcsub($this->value, (string) $amount, $scale + 1)))->mathRound($scale);
    }

    public function mathRound(int $scale = 2): AmountInterface
    {
        $this->guardNotNegativeScale($scale);

        $sign = '';
        if (bccomp('0', $this->value, 64) === '1') {
            $sign = '-';
        }

        $increment = sprintf('%s0.%s%s', $sign, str_repeat('0', $scale), '5');

        return new self(bcadd($this->value, $increment, $scale));
    }

    public function isGreaterThan(AmountInterface $amountToCompare, int $scale): bool
    {
        $this->guardNotNegativeScale($scale);

        return bccomp(
            (string) self::fromString($this->value)->mathRound($scale),
            (string) $amountToCompare->mathRound($scale), $scale
        ) === 1;
    }

    public function isLessThan(AmountInterface $amountToCompare, int $scale): bool
    {
        $this->guardNotNegativeScale($scale);

        return bccomp(
            (string) self::fromString($this->value)->mathRound($scale),
            (string) $amountToCompare->mathRound($scale), $scale
        ) === -1;
    }

    public function isEqualWith(AmountInterface $amountToCompare, int $scale): bool
    {
        $this->guardNotNegativeScale($scale);

        return bccomp(
            (string) self::fromString($this->value)->mathRound($scale),
            (string) $amountToCompare->mathRound($scale), $scale
        ) === 0;
    }
}
