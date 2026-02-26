<?php

namespace app\domain\entity;


final class Guest
{
    /**
     * @param GuestBooking[] $bookings
     * @param GuestAccount[] $accounts
     */
    public function __construct(
        public readonly int $guestId,
        public readonly string $guestType,
        public readonly string $firstName,
        public readonly ?string $middleName,
        public readonly string $lastName,
        public readonly string $gender,
        public readonly array $bookings,
        public readonly array $accounts,
    ) {}

}
