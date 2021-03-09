<?php

declare(strict_types=1);

namespace App\Core\Domain\Exception;

final class NegativeScale extends DomainViolation
{
    public function __construct(int $scale)
    {
        parent::__construct(sprintf('Scale cannot be negative ("%s" given)!', $scale));
    }
}
