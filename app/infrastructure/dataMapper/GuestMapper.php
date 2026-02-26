<?php

namespace app\infrastructure\dataMapper;

use app\domain\entity\Guest;
use app\domain\entity\GuestAccount;
use app\domain\entity\GuestBooking;

final class GuestMapper
{
    public function __construct(
        private readonly GuestBookingMapper $bookingMapper,
        private readonly GuestAccountMapper $accountMapper,
    ) {
    }

    public function fromArray(array $data): Guest
    {
        $bookings = array_map(
            fn(array $b) => $this->bookingMapper->fromArray($b),
            $data['guest_booking'] ?? [],
        );

        $accounts = array_map(
            fn(array $a) => $this->accountMapper->fromArray($a),
            $data['guest_account'] ?? [],
        );

        return new Guest(
            guestId: (int)$data['guest_id'],
            guestType: (string)$data['guest_type'],
            firstName: (string)$data['first_name'],
            middleName: array_key_exists(
                'middle_name',
                $data
            ) ? ($data['middle_name'] !== null ? (string)$data['middle_name'] : null) : null,
            lastName: (string)$data['last_name'],
            gender: (string)$data['gender'],
            bookings: $bookings,
            accounts: $accounts,
        );
    }

    public function toArray(Guest $guest): array
    {
        return [
            'guest_id'      => $guest->guestId,
            'guest_type'    => $guest->guestType,
            'first_name'    => $guest->firstName,
            'middle_name'   => $guest->middleName,
            'last_name'     => $guest->lastName,
            'gender'        => $guest->gender,
            'guest_booking' => array_map(
                static fn(GuestBooking $b): array => [
                    'booking_number' => $b->bookingNumber,
                    'ship_code'      => $b->shipCode,
                    'room_no'        => $b->roomNo,
                    'start_time'     => $b->startTime,
                    'end_time'       => $b->endTime,
                    'is_checked_in'  => $b->isCheckedIn,
                ],
                $guest->bookings,
            ),
            'guest_account' => array_map(
                static fn(GuestAccount $a): array => [
                    'account_id'    => $a->accountId,
                    'status_id'     => $a->statusId,
                    'account_limit' => $a->accountLimit,
                    'allow_charges' => $a->allowCharges,
                ],
                $guest->accounts,
            ),
        ];
    }

}
