<?php

namespace app\infrastructure\doctrine\dataMapper;

use app\domain\entity\GuestBooking;

final class GuestBookingMapper
{
    public function fromArray(array $data): GuestBooking
    {
        return new GuestBooking(
            bookingNumber: (int) $data['booking_number'],
            shipCode: isset($data['ship_code']) ? (string) $data['ship_code'] : null,
            roomNo: (string) $data['room_no'],
            startTime: isset($data['start_time']) ? (int) $data['start_time'] : null,
            endTime: isset($data['end_time']) ? (int) $data['end_time'] : null,
            isCheckedIn: (bool) $data['is_checked_in'],
        );
    }
}
