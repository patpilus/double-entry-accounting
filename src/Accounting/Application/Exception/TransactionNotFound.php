<?php

declare(strict_types=1);

namespace App\Accounting\Application\Exception;

use App\Core\Domain\Identifier\TransactionId;
use InvalidArgumentException;

class TransactionNotFound extends InvalidArgumentException
{
    public function __construct(TransactionId $transactionId)
    {
        parent::__construct(sprintf('Transaction with "%s" id does not exist.', (string) $transactionId));
    }
}
