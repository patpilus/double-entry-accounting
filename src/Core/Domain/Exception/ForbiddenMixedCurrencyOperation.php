<?php

declare(strict_types=1);

namespace App\Core\Domain\Exception;

final class ForbiddenMixedCurrencyOperation extends DomainViolation
{
    public function __construct()
    {
        parent::__construct(sprintf('Operations on objects of different currencies are not allowed!'));
    }
}
