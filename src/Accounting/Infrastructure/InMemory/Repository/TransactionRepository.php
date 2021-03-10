<?php

declare(strict_types=1);

namespace App\Accounting\Infrastructure\InMemory\Repository;

use App\Accounting\Application\Exception\TransactionNotFound;
use App\Accounting\Application\Repository\TransactionRepository as TransactionRepositoryInterface;
use App\Accounting\Domain\Transaction;
use App\Core\Domain\Identifier\TransactionId;
use Throwable;

class TransactionRepository implements TransactionRepositoryInterface
{
    private array $transactions;

    public function __construct()
    {
        $this->transactions = [];
    }

    public function save(Transaction $transaction): void
    {
        $this->transactions[(string) $transaction->getId()] = $transaction;
    }

    public function getById(TransactionId $transactionId): Transaction
    {
        try {
            return $this->transactions[(string) $transactionId];
        } catch (Throwable $exception) {
            throw new TransactionNotFound($transactionId);
        }
    }
}
