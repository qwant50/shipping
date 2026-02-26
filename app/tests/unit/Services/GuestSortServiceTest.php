<?php

namespace app\tests\unit\Services;

use app\domain\service\GuestSortService;

class GuestSortServiceTest extends \PHPUnit\Framework\TestCase
{
    private GuestSortService $service;

    protected function setUp(): void
    {
        $this->service = new GuestSortService();
    }

    // -------------------------------------------------------------------------
    // parseSortSpec
    // -------------------------------------------------------------------------

    public function testParseSortSpecSingleKeyAsc(): void
    {
        $result = $this->service->parseSortSpec('last_name:asc');

        $this->assertSame(['last_name' => SORT_ASC], $result);
    }

    public function testParseSortSpecSingleKeyDesc(): void
    {
        $result = $this->service->parseSortSpec('last_name:desc');

        $this->assertSame(['last_name' => SORT_DESC], $result);
    }

    public function testParseSortSpecDirectionIsCaseInsensitive(): void
    {
        $result = $this->service->parseSortSpec('last_name:DESC');

        $this->assertSame(['last_name' => SORT_DESC], $result);
    }

    public function testParseSortSpecKeyWithoutDirectionDefaultsToAsc(): void
    {
        $result = $this->service->parseSortSpec('last_name');

        $this->assertSame(['last_name' => SORT_ASC], $result);
    }

    public function testParseSortSpecMultipleKeys(): void
    {
        $result = $this->service->parseSortSpec('last_name:asc,account_id:desc');

        $this->assertSame(['last_name' => SORT_ASC, 'account_id' => SORT_DESC], $result);
    }

    public function testParseSortSpecTrimsWhitespace(): void
    {
        $result = $this->service->parseSortSpec(' last_name : desc , account_id : asc ');

        $this->assertSame(['last_name' => SORT_DESC, 'account_id' => SORT_ASC], $result);
    }

    public function testParseSortSpecEmptyStringReturnsEmptyArray(): void
    {
        $result = $this->service->parseSortSpec('');

        $this->assertSame([], $result);
    }

    // -------------------------------------------------------------------------
    // sort — top-level key
    // -------------------------------------------------------------------------

    public function testSortByTopLevelStringKeyAscending(): void
    {
        $data = [
            ['name' => 'Zara'],
            ['name' => 'Alice'],
            ['name' => 'Marco'],
        ];

        $sorted = $this->service->sort($data, ['name' => SORT_ASC]);

        $this->assertSame(['Alice', 'Marco', 'Zara'], array_column($sorted, 'name'));
    }

    public function testSortByTopLevelStringKeyDescending(): void
    {
        $data = [
            ['name' => 'Alice'],
            ['name' => 'Zara'],
            ['name' => 'Marco'],
        ];

        $sorted = $this->service->sort($data, ['name' => SORT_DESC]);

        $this->assertSame(['Zara', 'Marco', 'Alice'], array_column($sorted, 'name'));
    }

    public function testSortByTopLevelIntegerKey(): void
    {
        $data = [
            ['id' => 300],
            ['id' => 100],
            ['id' => 200],
        ];

        $sorted = $this->service->sort($data, ['id' => SORT_ASC]);

        $this->assertSame([100, 200, 300], array_column($sorted, 'id'));
    }

    public function testSortByMultipleTopLevelKeys(): void
    {
        $data = [
            ['last_name' => 'Burns',  'first_name' => 'Zara'],
            ['last_name' => 'Adams',  'first_name' => 'Marco'],
            ['last_name' => 'Burns',  'first_name' => 'Alice'],
        ];

        $sorted = $this->service->sort($data, ['last_name' => SORT_ASC, 'first_name' => SORT_ASC]);

        $this->assertSame('Adams', $sorted[0]['last_name']);
        $this->assertSame('Alice', $sorted[1]['first_name']);
        $this->assertSame('Zara',  $sorted[2]['first_name']);
    }

    // -------------------------------------------------------------------------
    // sort — nested key
    // -------------------------------------------------------------------------

    public function testSortSortsNestedListsByNestedKey(): void
    {
        $data = [
            [
                'name' => 'Alice',
                'accounts' => [
                    ['account_id' => 300],
                    ['account_id' => 100],
                    ['account_id' => 200],
                ],
            ],
        ];

        $sorted = $this->service->sort($data, ['account_id' => SORT_ASC]);

        $this->assertSame(
            [100, 200, 300],
            array_column($sorted[0]['accounts'], 'account_id'),
        );
    }

    public function testSortByKeyAtTopLevelAndNestedKeyInSamePass(): void
    {
        $data = [
            ['last_name' => 'Zara', 'accounts' => [['account_id' => 30], ['account_id' => 10]]],
            ['last_name' => 'Alice', 'accounts' => [['account_id' => 20], ['account_id' => 5]]],
        ];

        $sorted = $this->service->sort($data, ['last_name' => SORT_ASC, 'account_id' => SORT_ASC]);

        // top-level sorted by last_name
        $this->assertSame('Alice', $sorted[0]['last_name']);
        $this->assertSame('Zara',  $sorted[1]['last_name']);

        // nested accounts sorted by account_id within each guest
        $this->assertSame([5, 20],  array_column($sorted[0]['accounts'], 'account_id'));
        $this->assertSame([10, 30], array_column($sorted[1]['accounts'], 'account_id'));
    }

    // -------------------------------------------------------------------------
    // sort — edge cases
    // -------------------------------------------------------------------------

    public function testSortEmptyArrayReturnsEmpty(): void
    {
        $result = $this->service->sort([], ['name' => SORT_ASC]);

        $this->assertSame([], $result);
    }

    public function testSortWithUnknownKeyLeavesOrderUnchanged(): void
    {
        $data = [
            ['name' => 'Zara'],
            ['name' => 'Alice'],
        ];

        $sorted = $this->service->sort($data, ['nonexistent_key' => SORT_ASC]);

        $this->assertSame(['Zara', 'Alice'], array_column($sorted, 'name'));
    }

    public function testSortDoesNotMutatePassedArray(): void
    {
        $data = [
            ['name' => 'Zara'],
            ['name' => 'Alice'],
        ];

        $this->service->sort($data, ['name' => SORT_ASC]);

        $this->assertSame('Zara', $data[0]['name']);
        $this->assertSame('Alice', $data[1]['name']);
    }
}
