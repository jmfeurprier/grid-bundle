<?php

declare(strict_types=1);

namespace Jmf\Grid\Tests\Grid;

use Jmf\Grid\Grid\Column\Column;
use Jmf\Grid\Grid\Column\ColumnCollection;
use Jmf\Grid\Grid\Footer\Footer;
use Jmf\Grid\Grid\Grid;
use Jmf\Grid\Grid\Row\Row;
use Jmf\Grid\Grid\Row\RowCollection;
use PHPUnit\Framework\TestCase;

final class GridTest extends TestCase
{
    public function testGetColumnsReturnsDelegatedValues(): void
    {
        $column1 = new Column('Name', null);
        $column2 = new Column('Amount', 'right');
        $columns = new ColumnCollection([$column1, $column2]);
        $grid    = new Grid($columns, new RowCollection([]), new Footer([]));

        self::assertSame([$column1, $column2], iterator_to_array($grid->getColumns()));
    }

    public function testGetRowsReturnsDelegatedValues(): void
    {
        $row1 = new Row([], null);
        $row2 = new Row([], '/url');
        $rows = new RowCollection([$row1, $row2]);
        $grid = new Grid(new ColumnCollection([]), $rows, new Footer([]));

        self::assertSame([$row1, $row2], iterator_to_array($grid->getRows()));
    }

    public function testGetFooterReturnsSameInstance(): void
    {
        $footer = new Footer([]);
        $grid   = new Grid(new ColumnCollection([]), new RowCollection([]), $footer);

        self::assertSame($footer, $grid->getFooter());
    }
}
