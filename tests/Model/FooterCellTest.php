<?php

declare(strict_types=1);

namespace Jmf\Grid\Tests\Model;

use Jmf\Grid\Model\FooterCell;
use PHPUnit\Framework\TestCase;

final class FooterCellTest extends TestCase
{
    public function testGetValueReturnsValue(): void
    {
        $footerCell = new FooterCell('Total', []);

        self::assertSame('Total', $footerCell->getValue());
    }

    public function testGetAttributesReturnsEmpty(): void
    {
        $footerCell = new FooterCell('Total', []);

        self::assertSame([], $footerCell->getAttributes());
    }

    public function testGetAttributesReturnsValues(): void
    {
        $footerCell = new FooterCell(
            'Total',
            [
                'class'   => 'text-right',
                'colspan' => 3,
            ],
        );

        self::assertSame(
            [
                'class'   => 'text-right',
                'colspan' => 3,
            ],
            $footerCell->getAttributes(),
        );
    }
}
