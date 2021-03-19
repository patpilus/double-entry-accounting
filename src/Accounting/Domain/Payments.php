<?php

declare(strict_types=1);

namespace App\Accounting\Domain;

use App\Core\Domain\Amount as AmountInterface;
use App\Core\Domain\Amount\Amount;
use Countable;
use DateTimeImmutable;

use function array_filter;

class Payments implements Countable
{
    /**
     * @var Payment[]
     */
    private array $payments = [];
    private AmountInterface $balance;

    public function __construct(Payment ...$payments)
    {
        $this->balance = Amount::fromFloat(0);
        foreach ($payments as $payment) {
            $this->addPayment($payment);
        }
    }

    public function addPayment(Payment $payment): void
    {
        $this->payments[(string) $payment->getId()] = $payment;
        $this->balance = $this->balance->add($payment->getAmount());
    }

    /**
     * @return Payment[]
     */
    public function getEarlierThanOrEqualTo(DateTimeImmutable $date): array
    {
        return array_filter($this->payments, function (Payment $payment) use ($date): bool {
            return $payment->getCreatedAt() <= $date;
        });
    }

    /**
     * @return Payment[]
     */
    public function getLaterThan(DateTimeImmutable $date): array
    {
        return array_filter($this->payments, function (Payment $payment) use ($date): bool {
            return $payment->getCreatedAt() > $date;
        });
    }

    public function getBalance(): AmountInterface
    {
        return $this->balance;
    }

    /**
     * @return Payment[]
     */
    public function getPayments(): array
    {
        return $this->payments;
    }

    public function count(): int
    {
        return count($this->payments);
    }
}
