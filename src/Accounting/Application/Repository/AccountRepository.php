<?php

declare(strict_types=1);

namespace App\Accounting\Application\Repository;

use App\Accounting\Application\Exception\AccountNotFound;
use App\Accounting\Domain\Account;
use App\Core\Domain\Identifier\AccountId;

interface AccountRepository
{
    public function save(Account $account): void;

    /**
     * @throws AccountNotFound
     */
    public function getById(AccountId $accountId): Account;
}
