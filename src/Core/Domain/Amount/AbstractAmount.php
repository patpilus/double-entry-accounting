<?php

declare(strict_types=1);

namespace App\Core\Domain\Amount;

use App\Core\Domain\Amount;
use App\Core\Domain\Exception\NegativeScale;
use App\Core\Domain\Exception\NotNumericArgument;
use JsonSerializable;

abstract class AbstractAmount implements Amount, JsonSerializable
{
    /**
     * @var string
     */
    protected $value;

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
     * @throws NotNumericArgument
     */
    protected function guardNumericOnlyArgument(string $argument): void
    {
        if (!is_numeric($argument)) {
            throw new NotNumericArgument($argument);
        }
    }

    protected function guardNotNegativeScale(int $scale): void
    {
        if ($scale < 0) {
            throw new NegativeScale($scale);
        }
    }
}
