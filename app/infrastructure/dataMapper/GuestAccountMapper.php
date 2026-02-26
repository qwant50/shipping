<?php

namespace app\infrastructure\dataMapper;

use app\domain\entity\GuestAccount;

final class GuestAccountMapper
{
    public function fromArray(array $data): GuestAccount
    {
        return new GuestAccount(
            accountId: (int) $data['account_id'],
            statusId: isset($data['status_id']) ? (int) $data['status_id'] : null,
            accountLimit: (int) $data['account_limit'],
            allowCharges: (bool) $data['allow_charges'],
        );
    }
}
