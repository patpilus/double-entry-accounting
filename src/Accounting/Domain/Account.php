<?php

declare(strict_types=1);

namespace App\Accounting\Domain;

use App\Accounting\Domain\Exception\NegativeBalance;
use App\Core\Domain\Amount as AmountInterface;
use App\Core\Domain\Identifier\AccountId;
use DateTimeImmutable;

class Account
{
    private AccountId $id;
    private string $name;
    private AmountInterface $currentBalance;
    private DateTimeImmutable $currentBalanceUpdatedAt;
    private Payments $payments;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;
    private bool $isDebitAllowed;

    public function __construct(AccountId $id, string $name, bool $isDebitAllowed = false)
    {
        $this->id = $id;
        $this->name = $name;
        $this->currentBalance = AmountInterface\Amount::fromString('0.00');
        $this->payments = new Payments();
        $this->isDebitAllowed = $isDebitAllowed;

        $this->createdAt = new DateTimeImmutable();
        $this->currentBalanceUpdatedAt = new DateTimeImmutable();
        $this->updatedAt = $this->createdAt;
    }

    public function registerPayment(Payment $payment): void
    {
        $this->guardBalance($payment);
        $this->payments->addPayment($payment);

        $this->updatedAt = new DateTimeImmutable();
    }

    public function aggregateCurrentBalance(): void
    {
        foreach ($this->payments->getLaterThan($this->currentBalanceUpdatedAt) as $payment) {
            $this->currentBalance = $this->currentBalance->add($payment->getAmount());
            $this->currentBalanceUpdatedAt = $payment->getCreatedAt();
        }
    }

    public function getId(): AccountId
    {
        return $this->id;
    }

    public function getCurrentBalance(): AmountInterface
    {
        $currentBalance = $this->currentBalance;

        foreach ($this->payments->getLaterThan($this->currentBalanceUpdatedAt) as $payment) {
            $currentBalance = $currentBalance->add($payment->getAmount());
        }

        return $currentBalance;
    }

    public function getBalanceAsOf(DateTimeImmutable $date): AmountInterface
    {
        $balance = AmountInterface\Amount::fromString('0.00');

        foreach ($this->payments->getEarlierThanOrEqualTo($date) as $payment) {
            $balance = $balance->add($payment->getAmount());
        }

        return $balance;
    }

    private function guardBalance(Payment $payment): void
    {
        if ($this->isDebitAllowed) {
            return;
        }

        $balanceAfterPayment = $this->getCurrentBalance()->add($payment->getAmount());

        if ($balanceAfterPayment->isLessThan(AmountInterface\Amount::fromFloat(0), 2)) {
            throw new NegativeBalance($this->id, $payment->getId(), $payment->getAmount());
        }
    }
}
