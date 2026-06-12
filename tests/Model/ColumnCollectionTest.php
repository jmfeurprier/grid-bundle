<?php

declare(strict_types=1);

namespace Jmf\Grid\Tests\Model;

use Jmf\Grid\Model\Column;
use Jmf\Grid\Model\ColumnCollection;
use PHPUnit\Framework\TestCase;

final class ColumnCollectionTest extends TestCase
{
    public function testAllReturnsEmptyWhenNoColumns(): void
    {
        $columnCollection = new ColumnCollection([]);

        self::assertSame([], iterator_to_array($columnCollection->all()));
    }

    public function testAllReturnsSingleColumn(): void
    {
        $column           = new Column('Name', null);
        $columnCollection = new ColumnCollection([$column]);

        self::assertSame([$column], iterator_to_array($columnCollection->all()));
    }

    public function testAllReturnsMultipleColumns(): void
    {
        $column1          = new Column('Name', 'left');
        $column2          = new Column('Amount', 'right');
        $column3          = new Column(null, null);
        $columnCollection = new ColumnCollection(
            [
                $column1,
                $column2,
                $column3,
            ],
        );

        self::assertSame(
            [
                $column1,
                $column2,
                $column3,
            ],
            iterator_to_array($columnCollection->all()),
        );
    }
}
