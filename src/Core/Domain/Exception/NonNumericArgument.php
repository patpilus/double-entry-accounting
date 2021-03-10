<?php

declare(strict_types=1);

namespace App\Core\Domain\Exception;

final class NonNumericArgument extends DomainViolation
{
    public function __construct(string $argument)
    {
        parent::__construct(sprintf('Argument %s is not numeric!', $argument));
    }
}
