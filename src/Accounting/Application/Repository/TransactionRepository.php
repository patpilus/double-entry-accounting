<?php

declare(strict_types=1);

namespace App\Accounting\Application\Repository;

use App\Accounting\Application\Exception\TransactionNotFound;
use App\Accounting\Domain\Transaction;
use App\Core\Domain\Identifier\TransactionId;

interface TransactionRepository
{
    public function save(Transaction $transaction): void;

    /**
     * @throws TransactionNotFound
     */
    public function getById(TransactionId $transactionId): Transaction;
}
