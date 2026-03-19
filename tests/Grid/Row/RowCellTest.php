<?php

declare(strict_types=1);

namespace Jmf\Grid\Tests\Grid\Row;

use Jmf\Grid\Grid\Row\RowCell;
use PHPUnit\Framework\TestCase;

final class RowCellTest extends TestCase
{
    public function testGetValueReturnsValue(): void
    {
        $rowCell = new RowCell('foo', []);

        self::assertSame('foo', $rowCell->getValue());
    }

    public function testGetParametersReturnsEmpty(): void
    {
        $rowCell = new RowCell('foo', []);

        self::assertSame([], $rowCell->getParameters());
    }

    public function testGetParametersReturnsValues(): void
    {
        $rowCell = new RowCell('foo', ['align' => 'right']);

        self::assertSame(['align' => 'right'], $rowCell->getParameters());
    }
}
