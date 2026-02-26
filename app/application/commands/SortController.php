<?php

namespace app\application\commands;

use app\domain\service\GuestSortService;
use app\infrastructure\repository\GuestRepository;
use yii\base\Module;
use yii\console\ExitCode;

/**
 * Recursively sorts nested guest data by one or more keys at any depth.
 *
 * Usage:
 *   php yii sort --sort=last_name:asc
 *   php yii sort --sort=last_name:asc,account_id:desc
 */
class SortController extends BaseController
{
    public string $sort = '';

    public function __construct(
        string $id,
        Module $module,
        private readonly GuestRepository $guestRepository,
        private readonly GuestSortService $sortService,
        array $config = [],
    ) {
        parent::__construct($id, $module, $config);
    }

    public function options($actionID): array
    {
        return array_merge(parent::options($actionID), ['sort']);
    }

    public function actionIndex(): int
    {
        if ($this->sort === '') {
            $this->stdout("Usage: php yii sort --sort=last_name:asc,account_id:desc\n");
            return ExitCode::OK;
        }

        $data      = $this->guestRepository->loadGuests();
        $sortSpecs = $this->sortService->parseSortSpec($this->sort);
        $sorted    = $this->sortService->sort($data, $sortSpecs);

        $this->stdout("Sorting by: " . implode(', ', array_map(
            static fn($key, $direction) => "{$key}:" . ($direction === SORT_DESC ? 'desc' : 'asc'),
            array_keys($sortSpecs),
            $sortSpecs,
        )) . "\n\n");

        $this->printNested($sorted);

        return ExitCode::OK;
    }
}
