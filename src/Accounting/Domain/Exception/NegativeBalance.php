<?php

declare(strict_types=1);

namespace App\Accounting\Domain\Exception;

use App\Core\Domain\Amount;
use App\Core\Domain\Exception\DomainViolation;
use App\Core\Domain\Identifier\AccountId;
use App\Core\Domain\Identifier\PaymentId;

class NegativeBalance extends DomainViolation
{
    public function __construct(AccountId $accountId, PaymentId $paymentId, Amount $balanceAfterPayment)
    {
        parent::__construct(
            sprintf(
                'Debit for "%s" account is forbidden. Balance after payment "%s" would equal to %s, which is lower than 0',
                (string) $accountId,
                (string) $paymentId,
                $balanceAfterPayment->value()
            )
        );
    }
}