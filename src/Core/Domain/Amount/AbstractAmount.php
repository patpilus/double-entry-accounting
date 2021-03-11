<?php

declare(strict_types=1);

namespace App\Core\Domain\Amount;

use App\Core\Domain\Amount;
use App\Core\Domain\Exception\NegativeScale;
use App\Core\Domain\Exception\NonNumericArgument;
use JsonSerializable;

abstract class AbstractAmount implements Amount, JsonSerializable
{
    protected string $value;

    protected function __construct(string $value)
    {
        $this->guardNumericOnlyArgument($value);

        $this->value = $value;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function toFloat(): float
    {
        return (float) $this->value;
    }

    public function jsonSerialize(): array
    {
        return [
            'value' => $this->value
        ];
    }

    /**
     * @throws NonNumericArgument
     */
    protected function guardNumericOnlyArgument(string $argument): void
    {
        if (!is_numeric($argument)) {
            throw new NonNumericArgument($argument);
        }
    }

    protected function guardNonNegativeScale(int $scale): void
    {
        if ($scale < 0) {
            throw new NegativeScale($scale);
        }
    }
}
