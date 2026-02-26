<?php

namespace app\infrastructure\repository;

class GuestRepository
{
    public function loadGuests(): array
    {
        return require \Yii::getAlias('@app/data/sample_guests.php');
    }
}
