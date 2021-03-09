<?php

declare(strict_types=1);

namespace App\Core\Domain\Exception;

final class InvalidCurrencyCode extends DomainViolation
{
    public function __construct(string $currencyCode)
    {
        parent::__construct(sprintf('Currency code must contain three letters only ("%s" given)!', $currencyCode));
    }
}
