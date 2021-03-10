<?php

declare(strict_types=1);

namespace App\Accounting\Domain;

use App\Accounting\Domain\Exception\NonZeroBalance;
use App\Core\Domain\Amount\Amount;
use App\Core\Domain\Identifier\TransactionId;

class Transaction
{
    private TransactionId $id;
    private Payments $payments;

    public function __construct(TransactionId $id, Payments $payments)
    {
        $this->guardZeroBalance($payments, $id);

        $this->id = $id;
        $this->payments = $payments;
    }

    public function getId(): TransactionId
    {
        return $this->id;
    }

    public function getPayments(): Payments
    {
        return $this->payments;
    }

    private function guardZeroBalance(Payments $payments, TransactionId $transactionId): void
    {
        if (!$payments->getBalance()->isEqualWith(Amount::fromFloat(0), 2)) {
            throw new NonZeroBalance($transactionId, $payments->getBalance());
        }
    }
}
