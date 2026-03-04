<?php

namespace app\tests\unit\Domain;

use app\infrastructure\doctrine\dataMapper\GuestAccountMapper;
use app\infrastructure\doctrine\dataMapper\GuestBookingMapper;
use app\infrastructure\doctrine\dataMapper\GuestMapper;

class GuestTest extends \PHPUnit\Framework\TestCase
{
    public function testGuestFromArrayHydratesAllFields(): void
    {
        $data = [
            'guest_id'      => 177,
            'guest_type'    => 'crew',
            'first_name'    => 'Marco',
            'middle_name'   => null,
            'last_name'     => 'Burns',
            'gender'        => 'M',
            'guest_booking' => [
                [
                    'booking_number' => 20008683,
                    'ship_code'      => 'OST',
                    'room_no'        => 'A0073',
                    'start_time'     => 1438214400,
                    'end_time'       => 1483142400,
                    'is_checked_in'  => true,
                ],
            ],
            'guest_account' => [
                [
                    'account_id'    => 20009503,
                    'status_id'     => 2,
                    'account_limit' => 0,
                    'allow_charges' => true,
                ],
            ],
        ];

        $guest = new GuestMapper(new GuestBookingMapper(), new GuestAccountMapper())->fromArray($data);

        $this->assertSame(177, $guest->guestId);
        $this->assertSame('crew', $guest->guestType);
        $this->assertSame('Marco', $guest->firstName);
        $this->assertNull($guest->middleName);
        $this->assertSame('Burns', $guest->lastName);
        $this->assertSame('M', $guest->gender);
        $this->assertCount(1, $guest->bookings);
        $this->assertCount(1, $guest->accounts);
    }

    public function testMiddleNameNullStaysNull(): void
    {
        $data = [
            'guest_id'      => 1,
            'guest_type'    => 'crew',
            'first_name'    => 'Test',
            'middle_name'   => null,
            'last_name'     => 'User',
            'gender'        => 'M',
            'guest_booking' => [],
            'guest_account' => [],
        ];

        $guest = new GuestMapper(new GuestBookingMapper(), new GuestAccountMapper())->fromArray($data);

        $this->assertNull($guest->middleName);
    }

    public function testGuestBookingHandlesOptionalFields(): void
    {
        $data = [
            'booking_number' => 10000013,
            'room_no'        => 'B0092',
            'is_checked_in'  => true,
        ];

        $booking = new GuestBookingMapper()->fromArray($data);

        $this->assertSame(10000013, $booking->bookingNumber);
        $this->assertNull($booking->shipCode);
        $this->assertSame('B0092', $booking->roomNo);
        $this->assertNull($booking->startTime);
        $this->assertNull($booking->endTime);
        $this->assertTrue($booking->isCheckedIn);
    }

    public function testGuestBookingWithAllFields(): void
    {
        $data = [
            'booking_number' => 20008683,
            'ship_code'      => 'OST',
            'room_no'        => 'A0073',
            'start_time'     => 1438214400,
            'end_time'       => 1483142400,
            'is_checked_in'  => true,
        ];

        $booking = new GuestBookingMapper()->fromArray($data);

        $this->assertSame(20008683, $booking->bookingNumber);
        $this->assertSame('OST', $booking->shipCode);
        $this->assertSame('A0073', $booking->roomNo);
        $this->assertSame(1438214400, $booking->startTime);
        $this->assertSame(1483142400, $booking->endTime);
        $this->assertTrue($booking->isCheckedIn);
    }

    public function testGuestAccountHandlesOptionalStatusId(): void
    {
        $data = [
            'account_id'    => 10000522,
            'account_limit' => 300,
            'allow_charges' => true,
        ];

        $account = new GuestAccountMapper()->fromArray($data);

        $this->assertSame(10000522, $account->accountId);
        $this->assertNull($account->statusId);
        $this->assertSame(300, $account->accountLimit);
        $this->assertTrue($account->allowCharges);
    }

    public function testGuestAccountWithStatusId(): void
    {
        $data = [
            'account_id'    => 20009503,
            'status_id'     => 2,
            'account_limit' => 0,
            'allow_charges' => true,
        ];

        $account = new GuestAccountMapper()->fromArray($data);

        $this->assertSame(20009503, $account->accountId);
        $this->assertSame(2, $account->statusId);
        $this->assertSame(0, $account->accountLimit);
        $this->assertTrue($account->allowCharges);
    }
}
