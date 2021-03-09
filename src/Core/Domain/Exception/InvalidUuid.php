<?php

declare(strict_types=1);

namespace App\Core\Domain\Exception;

final class InvalidUuid extends DomainViolation
{
    public function __construct(string $uuid)
    {
        parent::__construct(sprintf('"%s" is not valid uuid!', $uuid));
    }
}
