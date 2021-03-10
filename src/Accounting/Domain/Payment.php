<?php

declare(strict_types=1);

namespace App\Accounting\Domain;

use App\Core\Domain\Amount;
use App\Core\Domain\Identifier\AccountId;
use App\Core\Domain\Identifier\PaymentId;
use App\Core\Domain\Identifier\TransactionId;
use DateTimeImmutable;

class Payment
{
    private PaymentId $id;
    private AccountId $accountId;
    private TransactionId $transactionId;
    private Amount $amount;
    private DateTimeImmutable $createdAt;

    public function __construct(
        PaymentId $id,
        AccountId $accountId,
        TransactionId $transactionId,
        Amount $amount,
        ?DateTimeImmutable $createdAt = null
    ) {
        $this->id = $id;
        $this->accountId = $accountId;
        $this->transactionId = $transactionId;
        $this->amount = $amount;
        $this->createdAt = $createdAt ?? new DateTimeImmutable();
    }

    public function getId(): PaymentId
    {
        return $this->id;
    }

    public function getAccountId(): AccountId
    {
        return $this->accountId;
    }

    public function getAmount(): Amount
    {
        return $this->amount;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
