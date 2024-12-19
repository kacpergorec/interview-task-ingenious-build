<?php
declare (strict_types=1);

namespace Shared\Domain;

use Symfony\Component\Uid\Uuid;

abstract class AggregateRootId implements \JsonSerializable
{
    protected Uuid $uuid;

    public function __construct(Uuid $uuid)
    {
        $this->uuid = $uuid;
    }

    public function toUuid(): Uuid
    {
        return $this->uuid;
    }

    public static function fromString(\Symfony\Component\Uid\UuidV7 $uuid): static
    {
        return new static(Uuid::fromString($uuid));
    }

    public function jsonSerialize(): string
    {
        return static::toUuid()->toString();
    }

    public function __toString(): string
    {
        return $this->uuid->toString();
    }

    public static function new(): static
    {
        return new static(Uuid::v7());
    }
}
