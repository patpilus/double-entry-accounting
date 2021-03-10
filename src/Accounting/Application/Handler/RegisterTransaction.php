<?php

declare(strict_types=1);

namespace App\Accounting\Application\Handler;

use App\Accounting\Application\Command;
use App\Accounting\Application\CommandHandler;
use App\Accounting\Application\Handler\RegisterTransaction\Command as RegisterTransactionCommand;
use App\Accounting\Application\Repository\AccountRepository;
use App\Accounting\Application\Repository\TransactionRepository;
use App\Accounting\Domain\Transaction;
use InvalidArgumentException;

class RegisterTransaction implements CommandHandler
{
    private TransactionRepository $transactionRepository;
    private AccountRepository $accountRepository;

    public function __construct(TransactionRepository $transactionRepository, AccountRepository $accountRepository)
    {
        $this->transactionRepository = $transactionRepository;
        $this->accountRepository = $accountRepository;
    }

    public function execute(Command $command): void
    {
        if (!$command instanceof RegisterTransactionCommand) {
            throw new InvalidArgumentException(sprintf('Command must be an instance of %s', RegisterTransactionCommand::class));
        }

        $transaction = new Transaction($command->getTransactionId(), $command->getPaymentCollection());

        foreach ($command->getPaymentCollection()->getPayments() as $payment) {
            $account = $this->accountRepository->getById($payment->getAccountId());
            $account->registerPayment($payment);

            $this->accountRepository->save($account);
        }

        $this->transactionRepository->save($transaction);
    }
}
