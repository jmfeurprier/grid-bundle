<?php

declare(strict_types=1);

namespace Jmf\Grid\Tests\Model;

use Jmf\Grid\Model\Row;
use Jmf\Grid\Model\RowCollection;
use PHPUnit\Framework\TestCase;

final class RowCollectionTest extends TestCase
{
    public function testAllReturnsEmpty(): void
    {
        $rowCollection = new RowCollection([]);

        self::assertSame([], iterator_to_array($rowCollection->all()));
    }

    public function testAllReturnsSingleRow(): void
    {
        $row           = new Row([], null);
        $rowCollection = new RowCollection([$row]);

        self::assertSame([$row], iterator_to_array($rowCollection->all()));
    }

    public function testAllReturnsMultipleRows(): void
    {
        $row1          = new Row([], null);
        $row2          = new Row([], '/url');
        $rowCollection = new RowCollection(
            [
                $row1,
                $row2,
            ],
        );

        self::assertSame(
            [
                $row1,
                $row2,
            ],
            iterator_to_array($rowCollection->all()),
        );
    }
}
