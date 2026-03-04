<?php

namespace app\infrastructure\cli\commands;

use yii\console\Controller;

abstract class BaseController extends Controller
{
    protected function printNested(array $data, int $depth = 0): void
    {
        $indent = str_repeat('  ', $depth);
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $this->stdout("{$indent}{$key}:\n");
                $this->printNested($value, $depth + 1);
            } else {
                $this->stdout("{$indent}{$key}: {$this->formatValue($value)}\n");
            }
        }
    }

    private function formatValue(mixed $value): string
    {
        if ($value === null) {
            return 'null';
        }
        if ($value === true) {
            return 'true';
        }
        if ($value === false) {
            return 'false';
        }
        return (string) $value;
    }
}
