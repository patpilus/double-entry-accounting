<?php

declare(strict_types=1);

namespace App\Core\Domain\Amount;

use App\Core\Domain\Amount as AmountInterface;
use App\Core\Domain\Exception\ForbiddenMixedCurrencyOperation;
use App\Core\Domain\Exception\InvalidCurrencyCode;

final class Money extends AbstractAmount
{
    /**
     * @var string
     * three-letter code compatible with ISO 4217
     */
    private $currencyCode;

    /**
     * @var int
     * required number of digits after decimal separator
     */
    private $currencyScale;

    protected function __construct(string $value, string $currencyCode, int $currencyScale = 2)
    {
        $this->guardNotNegativeScale($currencyScale);
        $this->guardCorrectCurrencyIso4217Code($currencyCode);

        $this->currencyCode = strtoupper($currencyCode);
        $this->currencyScale = $currencyScale;

        parent::__construct($this->mathRoundedValueToCurrencyScale($value));
    }

    public function currencyCode(): string
    {
        return $this->currencyCode;
    }

    public static function fromString(string $value, string $currencyCode, int $currencyScale = 2): self
    {
        return new self($value, $currencyCode, $currencyScale);
    }

    public static function fromFloat(float $value, string $currencyCode, int $currencyScale = 2): self
    {
        return new self((string) $value, $currencyCode, $currencyScale);
    }

    public static function fromArray(array $array): self
    {
        return Money::fromString(
            (string) $array['value'],
            $array['currencyCode'],
            (int) $array['currencyScale']
        );
    }

    /**
     * @return Money
     */
    public function add(AmountInterface $amount, int $scale = 2): AmountInterface
    {
        $this->guardNotNegativeScale($scale);
        $this->guardCurrencyCodeCoincidence($amount);

        return (new self(bcadd($this->value, (string) $amount, $scale + 1),
            $this->currencyCode,
            $this->currencyScale
        ))->mathRound($this->currencyScale);
    }

    /**
     * @return Money
     */
    public function subtract(AmountInterface $amount, int $scale = 2): AmountInterface
    {
        $this->guardNotNegativeScale($scale);
        $this->guardCurrencyCodeCoincidence($amount);

        return (new self(
            bcsub($this->value, (string) $amount, $scale + 1),
            $this->currencyCode,
            $this->currencyScale
        ))->mathRound($this->currencyScale);
    }

    public function mathRound(int $scale = 2): AmountInterface
    {
        $this->guardNotNegativeScale($scale);

        $sign = '';
        if (bccomp('0', $this->value, 64) === '1') {
            $sign = '-';
        }
        $increment = sprintf('%s0.%s%s', $sign, str_repeat('0', $scale), '5');

        return new self(bcadd($this->value, $increment, $scale), $this->currencyCode, $scale);
    }

    public function absolute(int $scale = 2): AmountInterface
    {
        $this->guardNotNegativeScale($scale);

        if (bccomp($this->value, '0', $scale) === -1) {
            return new self(bcmul($this->value, '-1', $scale), $this->currencyCode, $scale);
        }

        return new self($this->value, $this->currencyCode, $this->currencyScale);
    }

    public function isGreaterThan(AmountInterface $amountToCompare, int $scale): bool
    {
        $this->guardNotNegativeScale($scale);
        $this->guardCurrencyCodeCoincidence($amountToCompare);

        return bccomp(
            (string) self::fromString($this->value, $this->currencyCode, $this->currencyScale)->mathRound($scale),
            (string) $amountToCompare->mathRound($scale), $scale
        ) === 1;
    }

    public function isLessThan(AmountInterface $amountToCompare, int $scale): bool
    {
        $this->guardNotNegativeScale($scale);
        $this->guardCurrencyCodeCoincidence($amountToCompare);

        return bccomp(
            (string) self::fromString($this->value, $this->currencyCode, $this->currencyScale)->mathRound($scale),
            (string) $amountToCompare->mathRound($scale), $scale
        ) === -1;
    }

    public function isEqualWith(AmountInterface $amountToCompare, int $scale): bool
    {
        $this->guardNotNegativeScale($scale);
        $this->guardCurrencyCodeCoincidence($amountToCompare);

        return bccomp(
            (string) self::fromString($this->value, $this->currencyCode, $this->currencyScale)->mathRound($scale),
            (string) $amountToCompare->mathRound($scale), $scale
        ) === 0;
    }

    public function convertTo(string $convertionRate, string $currency, int $currencyScale = 2): Money
    {
        $this->guardNumericOnlyArgument($convertionRate);
        $this->guardNotNegativeScale($currencyScale);

        $money = (new self(
            bcmul($this->value, $convertionRate, $currencyScale + 1),
            $this->currencyCode,
            $currencyScale
        ))->mathRound($currencyScale);

        return new Money(
            $money->value(),
            $currency,
            $currencyScale
        );
    }

    public function jsonSerialize(): array
    {
        return array_merge(
            parent::jsonSerialize(),
            [
                'currencyCode' => $this->currencyCode,
                'currencyScale' => $this->currencyScale,
            ]
        );
    }

    private function mathRoundedValueToCurrencyScale(string $value): string
    {
        $this->guardNumericOnlyArgument($value);

        $sign = '';
        if (bccomp('0', $value, 64) === 1) {
            $sign = '-';
        }
        $increment = sprintf('%s0.%s%s', $sign, str_repeat('0', $this->currencyScale), '5');

        return bcadd($value, $increment, $this->currencyScale);
    }

    private function guardCorrectCurrencyIso4217Code(string $currencyCode): void
    {
        if (strlen($currencyCode) !== 3 || preg_match('/\\d/', $currencyCode) > 0) {
            throw new InvalidCurrencyCode($currencyCode);
        }
    }

    private function guardCurrencyCodeCoincidence(AmountInterface $amount): void
    {
        if ($amount instanceof Money && $amount->currencyCode !== $this->currencyCode) {
            throw new ForbiddenMixedCurrencyOperation();
        }
    }
}
