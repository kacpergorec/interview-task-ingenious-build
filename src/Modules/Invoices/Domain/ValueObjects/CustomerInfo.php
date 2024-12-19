<?php
declare (strict_types=1);

namespace Modules\Invoices\Domain\ValueObjects;

readonly class CustomerInfo
{
    public function __construct(
        public string $name,
        public string $email,
    )
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email address');
        }
    }

    public function isEqual(CustomerInfo $customerInfo): bool
    {
        return (
            $this->name === $customerInfo->name &&
            $this->email === $customerInfo->email
        );
    }
}
