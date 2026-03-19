<?php

declare(strict_types=1);

namespace Jmf\Grid\Tests\Grid\Column;

use Jmf\Grid\Grid\Column\Column;
use PHPUnit\Framework\TestCase;

final class ColumnTest extends TestCase
{
    public function testGetLabelReturnsValue(): void
    {
        $column = new Column('My Label', null);

        self::assertSame('My Label', $column->getLabel());
    }

    public function testGetLabelReturnsNull(): void
    {
        $column = new Column(null, null);

        self::assertNull($column->getLabel());
    }

    public function testGetAlignReturnsValue(): void
    {
        $column = new Column(null, 'center');

        self::assertSame('center', $column->getAlign());
    }

    public function testGetAlignReturnsNull(): void
    {
        $column = new Column(null, null);

        self::assertNull($column->getAlign());
    }

    public function testGettersWithBothValues(): void
    {
        $column = new Column('Name', 'right');

        self::assertSame('Name', $column->getLabel());
        self::assertSame('right', $column->getAlign());
    }
}