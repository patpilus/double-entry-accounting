<?php

declare(strict_types=1);

namespace App\Accounting\Infrastructure\InMemory\Repository;

use App\Accounting\Application\Exception\AccountNotFound;
use App\Accounting\Application\Repository\AccountRepository as AccountRepositoryInterface;
use App\Accounting\Domain\Account;
use App\Core\Domain\Identifier\AccountId;
use Throwable;

class AccountRepository implements AccountRepositoryInterface
{
    private array $accounts;

    public function __construct()
    {
        $this->accounts = [];
    }

    public function save(Account $account): void
    {
        $this->accounts[(string) $account->getId()] = $account;
    }

    public function getById(AccountId $accountId): Account
    {
        try {
            return $this->accounts[(string) $accountId];
        } catch (Throwable $exception) {
            throw new AccountNotFound($accountId);
        }
    }
}
