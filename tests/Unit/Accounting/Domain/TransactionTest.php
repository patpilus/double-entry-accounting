<?php

namespace App\Accounting\Domain;

use App\Accounting\Domain\Exception\NonZeroBalance;
use App\Core\Domain\Amount\Amount;
use App\Core\Domain\Identifier\AccountId;
use App\Core\Domain\Identifier\PaymentId;
use App\Core\Domain\Identifier\TransactionId;
use PHPUnit\Framework\TestCase;

class TransactionTest extends TestCase
{
    private TransactionId $transactionId;

    protected function setUp(): void
    {
        $this->transactionId = TransactionId::generate();
    }

    public function testAllowsToCreateTransactionWhichSumsUpToZero(): void
    {
        $transaction = new Transaction(TransactionId::generate(), $this->getValidTransactionPayments());

        self::assertInstanceOf(Transaction::class, $transaction);
    }

    public function testFailsToCreateTransactionWhichDoesNotSumUpToZero(): void
    {
        $this->expectException(NonZeroBalance::class);

        new Transaction(TransactionId::generate(), $this->getInvalidTransactionPayments());
    }

    private function getValidTransactionPayments(): Payments
    {
        $clientAccountId = AccountId::generate();
        $providerAccountId = AccountId::generate();
        $sellerAccountId = AccountId::generate();
        $bankAccountId = AccountId::generate();

        return new Payments(
            new Payment(PaymentId::generate(), $clientAccountId, $this->transactionId, Amount::fromString('-100')),
            new Payment(PaymentId::generate(), $providerAccountId, $this->transactionId, Amount::fromString('100')),
            new Payment(PaymentId::generate(), $providerAccountId, $this->transactionId, Amount::fromString('-99')),
            new Payment(PaymentId::generate(), $sellerAccountId, $this->transactionId, Amount::fromString('99')),
            new Payment(PaymentId::generate(), $providerAccountId, $this->transactionId, Amount::fromString('-0.5')),
            new Payment(PaymentId::generate(), $bankAccountId, $this->transactionId, Amount::fromString('0.5'))
        );
    }

    private function getInvalidTransactionPayments(): Payments
    {
        $clientAccountId = AccountId::generate();
        $providerAccountId = AccountId::generate();
        $sellerAccountId = AccountId::generate();

        return new Payments(
            new Payment(PaymentId::generate(), $clientAccountId, $this->transactionId, Amount::fromString('-100')),
            new Payment(PaymentId::generate(), $providerAccountId, $this->transactionId, Amount::fromString('100')),
            new Payment(PaymentId::generate(), $providerAccountId, $this->transactionId, Amount::fromString('-99')),
            new Payment(PaymentId::generate(), $sellerAccountId, $this->transactionId, Amount::fromString('99')),
            new Payment(PaymentId::generate(), $providerAccountId, $this->transactionId, Amount::fromString('-0.5')),
        );
    }
}
