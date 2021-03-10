<?php

declare(strict_types=1);

namespace App\Accounting\Application\Exception;

use App\Core\Domain\Identifier\AccountId;
use InvalidArgumentException;

class AccountNotFound extends InvalidArgumentException
{
    public function __construct(AccountId $accountId)
    {
        parent::__construct(sprintf('Account with "%s" id does not exist.', (string) $accountId));
    }
}
