<?php

declare(strict_types=1);

namespace App\Accounting\Domain;

use App\Core\Domain\Amount\Amount;
use App\Core\Domain\Identifier\AccountId;
use App\Core\Domain\Identifier\PaymentId;
use App\Core\Domain\Identifier\TransactionId;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class PaymentsTest extends TestCase
{
    public function testCanBeInitializedWithoutAnyPayment(): void
    {
        $payments = new Payments();

        self::assertInstanceOf(Payments::class, $payments);
        self::assertSame([], $payments->getPayments());
    }

    public function testAllowsToGetPaymentsEarlierThanOrEqualToRequestedDate(): void
    {
        $payments = $this->getValidPayments(AccountId::generate());

        self::assertCount(0, $payments->getEarlierThanOrEqualTo(new DateTimeImmutable('2020-12-31 23:59:59')));
        self::assertCount(1, $payments->getEarlierThanOrEqualTo(new DateTimeImmutable('2021-01-01')));
        self::assertCount(3, $payments->getEarlierThanOrEqualTo(new DateTimeImmutable('2022')));
    }

    public function testAllowsToGetPaymentsLaterThanRequestedDate(): void
    {
        $payments = $this->getValidPayments(AccountId::generate());

        self::assertCount(3, $payments->getLaterThan(new DateTimeImmutable('2020-12-31 23:59:59')));
        self::assertCount(2, $payments->getLaterThan(new DateTimeImmutable('2021-01-01')));
        self::assertCount(0, $payments->getLaterThan(new DateTimeImmutable('2022')));
    }

    private function getValidPayments(AccountId $accountId): Payments
    {
        return new Payments(
            new Payment(
                PaymentId::generate(),
                $accountId,
                TransactionId::generate(),
                Amount::fromString('100'),
                new DateTimeImmutable('2021-01-01')
            ),
            new Payment(
                PaymentId::generate(),
                $accountId,
                TransactionId::generate(),
                Amount::fromString('-25.16'),
                new DateTimeImmutable('2021-01-31')
            ),
            new Payment(
                PaymentId::generate(),
                $accountId,
                TransactionId::generate(),
                Amount::fromString('215.89'),
                new DateTimeImmutable('2021-02-01')
            ),
        );
    }
}
