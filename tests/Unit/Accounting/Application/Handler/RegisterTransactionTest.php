<?php

declare(strict_types=1);

namespace App\Accounting\Application\Handler;

use App\Accounting\Application\Exception\AccountNotFound;
use App\Accounting\Application\Handler\RegisterTransaction\Command as RegisterTransactionCommand;
use App\Accounting\Domain\Account;
use App\Accounting\Domain\Exception\NonZeroBalance;
use App\Accounting\Domain\Transaction;
use App\Accounting\Infrastructure\InMemory\Repository\AccountRepository;
use App\Accounting\Infrastructure\InMemory\Repository\TransactionRepository;
use App\Core\Domain\Identifier\AccountId;
use App\Core\Domain\Identifier\TransactionId;
use PHPUnit\Framework\TestCase;

class RegisterTransactionTest extends TestCase
{
    private RegisterTransaction $registerTransactionHandler;
    private TransactionRepository $transactionRepository;
    private AccountRepository $accountRepository;
    private AccountId $clientAccountId;
    private AccountId $sellerAccountId;
    private TransactionId $transactionId;

    protected function setUp(): void
    {
        $this->registerTransactionHandler = new RegisterTransaction(
            $this->transactionRepository = new TransactionRepository(),
            $this->accountRepository = new AccountRepository()
        );

        $this->clientAccountId = AccountId::generate();
        $clientAccount = new Account($this->clientAccountId, 'Płatnik', true);
        $this->accountRepository->save($clientAccount);

        $this->sellerAccountId = AccountId::generate();
        $sellerAccount = new Account($this->sellerAccountId, 'Sprzedawca', false);
        $this->accountRepository->save($sellerAccount);
    }

    public function testAllowsToTransferAmountFromOneAccountToAnother(): void
    {
        $this->registerTransactionHandler->execute($this->getClientToSellerTransactionCommand());

        $transaction = $this->transactionRepository->getById($this->transactionId);
        self::assertInstanceOf(Transaction::class, $transaction);
        self::assertCount(2, $transaction->getPayments());

        $clientAccount = $this->accountRepository->getById($this->clientAccountId);
        self::assertSame('-100.00', $clientAccount->getCurrentBalance()->value());

        $sellerAccount = $this->accountRepository->getById($this->sellerAccountId);
        self::assertSame('100.00', $sellerAccount->getCurrentBalance()->value());
    }

    public function testFailsToRegisterInconsistentTransaction(): void
    {
        $this->expectException(NonZeroBalance::class);
        $this->registerTransactionHandler->execute($this->getInvalidClientToSellerTransactionCommand());

        $clientAccount = $this->accountRepository->getById($this->clientAccountId);
        self::assertSame('0.00', $clientAccount->getCurrentBalance()->value());

        $sellerAccount = $this->accountRepository->getById($this->sellerAccountId);
        self::assertSame('0.00', $sellerAccount->getCurrentBalance()->value());
    }

    public function testFailsToRegisterTransactionForNonExistingAccount(): void
    {
        $this->expectException(AccountNotFound::class);
        $this->registerTransactionHandler->execute($this->getNonExistingClientToSellerTransactionCommand());
    }

    private function getClientToSellerTransactionCommand(): RegisterTransactionCommand
    {
        $this->transactionId = TransactionId::generate();

        return new RegisterTransactionCommand(
            (string) $this->transactionId,
            [
                'id' => 'bb699729-730c-4c91-a55c-c735b9a79241',
                'accountId' => (string) $this->clientAccountId,
                'amount' => '-100'
            ],
            [
                'id' => 'b3cc40c6-2777-4056-b1fc-edc4f13908f0',
                'accountId' => (string) $this->sellerAccountId,
                'amount' => '100'
            ],
        );
    }

    private function getInvalidClientToSellerTransactionCommand(): RegisterTransactionCommand
    {
        $this->transactionId = TransactionId::generate();

        return new RegisterTransactionCommand(
            (string) $this->transactionId,
            [
                'id' => 'c600119c-8b8e-4715-9475-8cc03347ae80',
                'accountId' => (string) $this->clientAccountId,
                'amount' => '-100'
            ],
            [
                'id' => '037fee51-3ae4-4ce0-aa99-57acd305c2a2',
                'accountId' => (string) $this->sellerAccountId,
                'amount' => '95'
            ],
        );
    }

    private function getNonExistingClientToSellerTransactionCommand(): RegisterTransactionCommand
    {
        $this->transactionId = TransactionId::generate();

        return new RegisterTransactionCommand(
            (string) $this->transactionId,
            [
                'id' => 'c600119c-8b8e-4715-9475-8cc03347ae80',
                'accountId' => (string) AccountId::generate(),
                'amount' => '-100'
            ],
            [
                'id' => '037fee51-3ae4-4ce0-aa99-57acd305c2a2',
                'accountId' => (string) $this->sellerAccountId,
                'amount' => '100'
            ],
        );
    }
}
