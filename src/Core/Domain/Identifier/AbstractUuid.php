<?php

declare(strict_types=1);

namespace App\Core\Domain\Identifier;

use App\Core\Domain\Exception\InvalidUuid;
use JsonSerializable;
use Ramsey\Uuid\Uuid;

abstract class AbstractUuid implements AggregateId, JsonSerializable
{
    /**
     * @var string
     */
    private $id;

    protected function __construct(string $id)
    {
        if (!Uuid::isValid($id)) {
            throw new InvalidUuid($id);
        }
        $this->id = $id;
    }

    /**
     * @return static
     */
    public static function generate(): AggregateId
    {
        return new static((string) Uuid::uuid4());
    }

    /**
     * @return static
     */
    public static function fromString(string $id): AggregateId
    {
        return new static($id);
    }

    public function __toString(): string
    {
        return $this->id;
    }

    public function isEqualWith(AggregateId $id): bool
    {
        return (string) $id === $this->id;
    }

    public static function isValid(string $id): bool
    {
        return Uuid::isValid($id);
    }

    public function jsonSerialize(): string
    {
        return $this->id;
    }
}
