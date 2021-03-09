<?php

declare(strict_types=1);

namespace App\Core\Domain;

use App\Core\Domain\Exception\NotNumericArgument;

interface Amount
{
    /**
     * @throws NotNumericArgument
     */
    public function add(Amount $amount, int $scale = 2): self;
    public function subtract(Amount $amount, int $scale = 2): self;
    public function isGreaterThan(Amount $amountToCompare, int $scale): bool;
    public function isLessThan(Amount $amountToCompare, int $scale): bool;
    public function isEqualWith(Amount $amountToCompare, int $scale): bool;
    public function value(): string;
    public function __toString(): string;
}
