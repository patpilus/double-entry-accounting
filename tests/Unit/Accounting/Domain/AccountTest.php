<?php

namespace App\Accounting\Domain;

use App\Accounting\Domain\Exception\NegativeBalance;
use App\Core\Domain\Amount\Amount;
use App\Core\Domain\Identifier\AccountId;
use App\Core\Domain\Identifier\PaymentId;
use App\Core\Domain\Identifier\TransactionId;
use PHPUnit\Framework\TestCase;

class AccountTest extends TestCase
{
    public function testFailsToHaveNegativeBalanceForNonDebitAccount(): void
    {
        $account = new Account(AccountId::generate(), 'Sprzedawca', false);

        $account->registerPayment(
            new Payment(PaymentId::generate(), $account->getId(), TransactionId::generate(), Amount::fromString('100'))
        );

        $this->expectException(NegativeBalance::class);
        $account->registerPayment(
            new Payment(PaymentId::generate(), $account->getId(), TransactionId::generate(), Amount::fromString('-101'))
        );
    }

    public function testAllowsToHaveNegativeBalanceForDebitAccount(): void
    {
        $account = new Account(AccountId::generate(), 'Płatnik', true);

        $account->registerPayment(
            new Payment(PaymentId::generate(), $account->getId(), TransactionId::generate(), Amount::fromString('-100'))
        );

        self::assertSame('-100.00', $account->getCurrentBalance()->value());
    }

    public function testAllowsToGetCurrentBalance(): void
    {
        $account = new Account(AccountId::generate(), 'Sprzedawca', false);

        $account->registerPayment(
            new Payment(PaymentId::generate(), $account->getId(), TransactionId::generate(), Amount::fromString('1510.12'))
        );
        $account->registerPayment(
            new Payment(PaymentId::generate(), $account->getId(), TransactionId::generate(), Amount::fromString('100.14'))
        );
        $account->registerPayment(
            new Payment(PaymentId::generate(), $account->getId(), TransactionId::generate(), Amount::fromString('-12.15'))
        );

        self::assertSame('1598.11', $account->getCurrentBalance()->value());
    }

    public function testAllowsToGetBalanceAtAnyTimeOfTheHistory(): void
    {
        $account = new Account(AccountId::generate(), 'Sprzedawca 2', false);

        $account->registerPayment(
            new Payment(PaymentId::generate(), $account->getId(), TransactionId::generate(), Amount::fromString
            ('1315.22'), new \DateTimeImmutable('2021-01-01 00:00:15'))
        );
        $account->registerPayment(
            new Payment(PaymentId::generate(), $account->getId(), TransactionId::generate(), Amount::fromString('9566.10'), new \DateTimeImmutable('2021-01-01 00:01:12'))
        );
        $account->registerPayment(
            new Payment(PaymentId::generate(), $account->getId(), TransactionId::generate(), Amount::fromString('1001.03'), new \DateTimeImmutable('2021-02-23'))
        );

        self::assertSame('0.00', $account->getBalanceAsOf(new \DateTimeImmutable('2020-12-31'))->value());
        self::assertSame('0.00', $account->getBalanceAsOf(new \DateTimeImmutable('2021-01-01 00:00:14'))->value());

        self::assertSame('1315.22', $account->getBalanceAsOf(new \DateTimeImmutable('2021-01-01 00:00:15'))->value());
        self::assertSame('1315.22', $account->getBalanceAsOf(new \DateTimeImmutable('2021-01-01 00:01:11'))->value());

        self::assertSame('10881.32', $account->getBalanceAsOf(new \DateTimeImmutable('2021-01-01 00:01:12'))->value());
        self::assertSame('10881.32', $account->getBalanceAsOf(new \DateTimeImmutable('2021-02-22 23:59:59'))->value());

        self::assertSame('11882.35', $account->getBalanceAsOf(new \DateTimeImmutable('2021-02-23'))->value());
        self::assertSame('11882.35', $account->getBalanceAsOf(new \DateTimeImmutable('2022'))->value());
    }
}
