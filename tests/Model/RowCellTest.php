<?php

declare(strict_types=1);

namespace Jmf\Grid\Tests\Model;

use Jmf\Grid\Model\RowCell;
use PHPUnit\Framework\TestCase;

final class RowCellTest extends TestCase
{
    public function testGetValueReturnsValue(): void
    {
        $rowCell = new RowCell('foo', null);

        self::assertSame('foo', $rowCell->getValue());
    }

    public function testGetAlignReturnsNull(): void
    {
        $rowCell = new RowCell('foo', null);

        self::assertNull($rowCell->getAlign());
    }

    public function testGetAlignReturnsValue(): void
    {
        $rowCell = new RowCell('foo', 'right');

        self::assertSame('right', $rowCell->getAlign());
    }
}
