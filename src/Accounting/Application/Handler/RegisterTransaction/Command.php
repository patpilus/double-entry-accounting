<?php

declare(strict_types=1);

namespace App\Accounting\Application\Handler\RegisterTransaction;

use App\Accounting\Application\Command as CommandInterface;
use App\Accounting\Domain\Payment;
use App\Accounting\Domain\Payments;
use App\Core\Domain\Amount\Amount;
use App\Core\Domain\Identifier\AccountId;
use App\Core\Domain\Identifier\PaymentId;
use App\Core\Domain\Identifier\TransactionId;
use InvalidArgumentException;
use Throwable;

class Command implements CommandInterface
{
    private string $transactionId;
    /**
     * @var array[] {
     *  @type string $id The id of payment
     *  @type string $accountId
     *  @type string|float $amount The payment amount
     * }
     */
    private array $payments;

    public function __construct(string $transactionId, array ...$payments)
    {
        $this->transactionId = $transactionId;
        $this->payments = $payments;
    }

    public function getTransactionId(): TransactionId
    {
        return TransactionId::fromString($this->transactionId);
    }

    public function getPaymentCollection(): Payments
    {
        try {
            $payments = array_map(function (array $payment): Payment {
                return new Payment(
                    PaymentId::fromString($payment['id']),
                    AccountId::fromString($payment['accountId']),
                    $this->getTransactionId(),
                    Amount::fromString((string) $payment['amount'])
                );
            }, $this->payments);
        } catch (Throwable $exception) {
            throw new InvalidArgumentException($exception->getMessage());
        }

        return new Payments(...$payments);
    }
}
