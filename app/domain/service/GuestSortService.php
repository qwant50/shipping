<?php

namespace app\domain\service;

class GuestSortService
{
    /** @return array<string, int> key => SORT_ASC|SORT_DESC */
    public function parseSortSpec(string $sort): array
    {
        $specs = [];
        foreach (explode(',', $sort) as $part) {
            $part = trim($part);
            if ($part === '') {
                continue;
            }
            if (str_contains($part, ':')) {
                [$key, $dir] = explode(':', $part, 2);
                $specs[trim($key)] = strtolower(trim($dir)) === 'desc' ? SORT_DESC : SORT_ASC;
            } else {
                $specs[$part] = SORT_ASC;
            }
        }
        return $specs;
    }

    public function sort(array $data, array $sortSpecs): array
    {
        $this->recursiveSort($data, $sortSpecs);
        return $data;
    }

    private function recursiveSort(array &$data, array $sortSpecs): void
    {
        if (array_is_list($data) && !empty($data) && is_array($data[0])) {
            $applicable = [];
            foreach ($sortSpecs as $key => $direction) {
                foreach ($data as $item) {
                    if (is_array($item) && array_key_exists($key, $item)) {
                        $applicable[$key] = $direction;
                        break;
                    }
                }
            }

            if (!empty($applicable)) {
                $args = [];
                foreach ($applicable as $key => $direction) {
                    $args[] = array_column($data, $key);
                    $args[] = $direction;
                    $args[] = SORT_REGULAR;
                }
                $args[] = &$data;
                array_multisort(...$args);
            }
        }

        foreach ($data as &$value) {
            if (is_array($value)) {
                $this->recursiveSort($value, $sortSpecs);
            }
        }
        unset($value);
    }
}
