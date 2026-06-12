<?php

declare(strict_types=1);

namespace Jmf\Grid\Tests\Model;

use Jmf\Grid\Model\FooterCell;
use Jmf\Grid\Model\FooterRow;
use PHPUnit\Framework\TestCase;

final class FooterRowTest extends TestCase
{
    public function testGetCellsReturnsEmpty(): void
    {
        $row = new FooterRow([]);

        self::assertSame([], iterator_to_array($row->getCells()));
    }

    public function testGetCellsReturnsSingleCell(): void
    {
        $cell = new FooterCell('Total', [], null);
        $row  = new FooterRow([$cell]);

        self::assertSame([$cell], iterator_to_array($row->getCells()));
    }

    public function testGetCellsReturnsMultipleCells(): void
    {
        $cell1 = new FooterCell('Label', [], null);
        $cell2 = new FooterCell('42', [], 'right');
        $row   = new FooterRow([$cell1, $cell2]);

        self::assertSame([$cell1, $cell2], iterator_to_array($row->getCells()));
    }
}
