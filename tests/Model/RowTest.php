<?php

declare(strict_types=1);

namespace Jmf\Grid\Tests\Model;

use Jmf\Grid\Model\Row;
use Jmf\Grid\Model\RowCell;
use PHPUnit\Framework\TestCase;

final class RowTest extends TestCase
{
    public function testGetCellsReturnsEmpty(): void
    {
        $row = new Row([], null);

        self::assertSame([], iterator_to_array($row->getCells()));
    }

    public function testGetCellsReturnsCells(): void
    {
        $cell1 = new RowCell('foo', []);
        $cell2 = new RowCell('bar', ['align' => 'right']);
        $row   = new Row([$cell1, $cell2], null);

        self::assertSame([$cell1, $cell2], iterator_to_array($row->getCells()));
    }

    public function testGetLinkReturnsNull(): void
    {
        $row = new Row([], null);

        self::assertNull($row->getLink());
    }

    public function testGetLinkReturnsValue(): void
    {
        $row = new Row([], '/some/url');

        self::assertSame('/some/url', $row->getLink());
    }

    public function testGetAttributesReturnsEmpty(): void
    {
        $row = new Row([], null);

        self::assertSame([], $row->getAttributes());
    }

    public function testGetAttributesReturnsValues(): void
    {
        $row = new Row([], null, ['class' => 'active']);

        self::assertSame(['class' => 'active'], $row->getAttributes());
    }
}
