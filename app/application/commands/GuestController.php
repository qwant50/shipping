<?php

namespace app\application\commands;

use app\infrastructure\repository\GuestRepository;
use yii\base\Module;
use yii\console\ExitCode;

class GuestController extends BaseController
{
    public function __construct(
        string $id,
        Module $module,
        private readonly GuestRepository $guestRepository,
        array $config = [],
    ) {
        parent::__construct($id, $module, $config);
    }

    public function actionIndex(): int
    {
        $guests = $this->guestRepository->loadGuests();

        foreach ($guests as $guest) {
            $this->printNested($guest);
            $this->stdout("\n");
        }

        return ExitCode::OK;
    }
}
