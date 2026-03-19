<?php

declare(strict_types=1);

namespace Jmf\Grid\Tests\Grid\Footer;

use Jmf\Grid\Grid\Footer\Footer;
use Jmf\Grid\Grid\Footer\FooterCell;
use Jmf\Grid\Grid\Footer\FooterRow;
use PHPUnit\Framework\TestCase;

final class FooterTest extends TestCase
{
    public function testGetRowsReturnsEmpty(): void
    {
        $footer = new Footer([]);

        self::assertSame([], iterator_to_array($footer->getRows()));
    }

    public function testGetRowsReturnsSingleRow(): void
    {
        $footerRow = new FooterRow([new FooterCell('Total', [])]);
        $footer    = new Footer([$footerRow]);

        self::assertSame([$footerRow], iterator_to_array($footer->getRows()));
    }

    public function testGetRowsReturnsMultipleRows(): void
    {
        $row1   = new FooterRow([new FooterCell('Subtotal', [])]);
        $row2   = new FooterRow([new FooterCell('Total', [])]);
        $footer = new Footer(
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
            iterator_to_array($footer->getRows()),
        );
    }
}
