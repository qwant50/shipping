<?php

namespace app\domain\entity;

final class GuestBooking
{
    public function __construct(
        public readonly int $bookingNumber,
        public readonly ?string $shipCode,
        public readonly string $roomNo,
        public readonly ?int $startTime,
        public readonly ?int $endTime,
        public readonly bool $isCheckedIn,
    ) {}

}
