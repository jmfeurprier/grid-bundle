<?php

declare(strict_types=1);

namespace Jmf\Grid\Tests\Model;

use Jmf\Grid\Model\FooterCell;
use PHPUnit\Framework\TestCase;

final class FooterCellTest extends TestCase
{
    public function testGetValueReturnsValue(): void
    {
        $footerCell = new FooterCell('Total', [], null);

        self::assertSame('Total', $footerCell->getValue());
    }

    public function testGetAttributesReturnsEmpty(): void
    {
        $footerCell = new FooterCell('Total', [], null);

        self::assertSame([], $footerCell->getAttributes());
    }

    public function testGetAttributesReturnsValues(): void
    {
        $footerCell = new FooterCell(
            'Total',
            [
                'colspan' => 3,
            ],
            null,
        );

        self::assertSame(
            [
                'colspan' => 3,
            ],
            $footerCell->getAttributes(),
        );
    }

    public function testGetAlignReturnsNull(): void
    {
        $footerCell = new FooterCell('Total', [], null);

        self::assertNull($footerCell->getAlign());
    }

    public function testGetAlignReturnsValue(): void
    {
        $footerCell = new FooterCell('Total', [], 'right');

        self::assertSame('right', $footerCell->getAlign());
    }
}
