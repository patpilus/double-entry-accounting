<?php

declare(strict_types=1);

namespace App\Accounting\Domain\Exception;

use App\Core\Domain\Amount;
use App\Core\Domain\Exception\DomainViolation;
use App\Core\Domain\Identifier\TransactionId;

class NonZeroBalance extends DomainViolation
{
    public function __construct(TransactionId $transactionId, Amount $amount)
    {
        parent::__construct(
            sprintf(
                'Transaction "%s" total balance must sum up to 0, %s calculated',
                (string) $transactionId,
                $amount->value()
            )
        );
    }
}
