<?php

declare(strict_types=1);

namespace App\Core\Domain;

use App\Core\Domain\Exception\NonNumericArgument;

interface Amount
{
    /**
     * @throws NonNumericArgument
     */
    public function add(Amount $amount, int $scale = 2): self;
    public function subtract(Amount $amount, int $scale = 2): self;
    public function value(): string;
    public function __toString(): string;
}
