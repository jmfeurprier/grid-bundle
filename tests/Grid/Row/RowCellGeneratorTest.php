<?php

declare(strict_types=1);

namespace Jmf\Grid\Tests\Grid\Row;

use Jmf\Grid\Configuration\Column\ColumnConfiguration;
use Jmf\Grid\Exception\UnexpectedValueTypeException;
use Jmf\Grid\Grid\Row\RowCellGenerator;
use Jmf\TemplateRendering\StringTemplate;
use Jmf\TemplateRendering\TemplateRendererInterface;
use PHPUnit\Framework\TestCase;
use stdClass;
use Stringable;
use Symfony\Component\PropertyAccess\PropertyAccess;

final class RowCellGeneratorTest extends TestCase
{
    public function testGenerateWithArrayItemAndSource(): void
    {
        $columnConfig = new ColumnConfiguration(align: null, label: null, source: '[name]', template: null);

        $cell = $this->createRowCellGenerator()->generate($columnConfig, ['name' => 'Alice'], []);

        self::assertSame('Alice', $cell->getValue());
    }

    public function testGenerateWithObjectItemAndSource(): void
    {
        $item         = new class {
            public string $name = 'Bob';
        };
        $columnConfig = new ColumnConfiguration(align: null, label: null, source: 'name', template: null);

        $cell = $this->createRowCellGenerator()->generate($columnConfig, $item, []);

        self::assertSame('Bob', $cell->getValue());
    }

    public function testGenerateWithNoSourceReturnsEmptyString(): void
    {
        $columnConfig = new ColumnConfiguration(align: null, label: null, source: null, template: null);

        $cell = $this->createRowCellGenerator()->generate($columnConfig, [], []);

        self::assertSame('', $cell->getValue());
    }

    public function testGenerateWithMissingArrayKeyReturnsEmptyString(): void
    {
        $columnConfig = new ColumnConfiguration(align: null, label: null, source: '[missing]', template: null);

        $cell = $this->createRowCellGenerator()->generate($columnConfig, [], []);

        self::assertSame('', $cell->getValue());
    }

    public function testGenerateWithIntegerValueConvertsToString(): void
    {
        $columnConfig = new ColumnConfiguration(align: null, label: null, source: '[count]', template: null);

        $cell = $this->createRowCellGenerator()->generate($columnConfig, ['count' => 42], []);

        self::assertSame('42', $cell->getValue());
    }

    public function testGenerateWithBoolValueConvertsToString(): void
    {
        $columnConfig = new ColumnConfiguration(align: null, label: null, source: '[active]', template: null);

        $cell = $this->createRowCellGenerator()->generate($columnConfig, ['active' => true], []);

        self::assertSame('1', $cell->getValue());
    }

    public function testGenerateWithStringableValueConvertsToString(): void
    {
        $stringable = new class implements Stringable {
            public function __toString(): string
            {
                return 'stringable-value';
            }
        };

        $columnConfig = new ColumnConfiguration(align: null, label: null, source: '[obj]', template: null);

        $cell = $this->createRowCellGenerator()->generate($columnConfig, ['obj' => $stringable], []);

        self::assertSame('stringable-value', $cell->getValue());
    }

    public function testGenerateWithNullValueReturnsEmptyString(): void
    {
        $columnConfig = new ColumnConfiguration(align: null, label: null, source: '[val]', template: null);

        $cell = $this->createRowCellGenerator()->generate($columnConfig, ['val' => null], []);

        self::assertSame('', $cell->getValue());
    }

    public function testGenerateTrimsValue(): void
    {
        $columnConfig = new ColumnConfiguration(align: null, label: null, source: '[name]', template: null);

        $cell = $this->createRowCellGenerator()->generate($columnConfig, ['name' => '  Alice  '], []);

        self::assertSame('Alice', $cell->getValue());
    }

    public function testGenerateWithNonStringableObjectThrowsException(): void
    {
        $columnConfig = new ColumnConfiguration(align: null, label: null, source: '[obj]', template: null);

        $this->expectException(UnexpectedValueTypeException::class);

        $this->createRowCellGenerator()->generate($columnConfig, ['obj' => new stdClass()], []);
    }

    public function testGenerateWithAlignSetsParameter(): void
    {
        $columnConfig = new ColumnConfiguration(align: 'right', label: null, source: null, template: null);

        $cell = $this->createRowCellGenerator()->generate($columnConfig, [], []);

        self::assertSame(['align' => 'right'], $cell->getParameters());
    }

    public function testGenerateWithNoAlignReturnsEmptyParameters(): void
    {
        $columnConfig = new ColumnConfiguration(align: null, label: null, source: null, template: null);

        $cell = $this->createRowCellGenerator()->generate($columnConfig, [], []);

        self::assertSame([], $cell->getParameters());
    }

    public function testGenerateWithTemplate(): void
    {
        $template     = new StringTemplate('{{ _value|upper }}');
        $columnConfig = new ColumnConfiguration(align: null, label: null, source: '[name]', template: $template);

        $renderer = $this->createMock(TemplateRendererInterface::class);
        $renderer
            ->expects(self::once())
            ->method('render')
            ->with($template, self::arrayHasKey('_value'))
            ->willReturn('ALICE')
        ;

        $cell = $this->createRowCellGenerator($renderer)->generate($columnConfig, ['name' => 'Alice'], []);

        self::assertSame('ALICE', $cell->getValue());
    }

    public function testGenerateWithTemplateReceivesSourceValueAsUnderscoreValue(): void
    {
        $template     = new StringTemplate('{{ _value }}');
        $columnConfig = new ColumnConfiguration(align: null, label: null, source: '[name]', template: $template);

        $renderer = $this->createMock(TemplateRendererInterface::class);
        $renderer
            ->expects(self::once())
            ->method('render')
            ->with(
                $template,
                self::callback(fn(
                    array $ctx,
                ): bool => $ctx['_value'] === 'Alice'),
            )
            ->willReturn('Alice')
        ;

        $this->createRowCellGenerator($renderer)->generate($columnConfig, ['name' => 'Alice'], []);
    }

    public function testGenerateWithTemplateReceivesRowVariables(): void
    {
        $template     = new StringTemplate('{{ currency }}');
        $columnConfig = new ColumnConfiguration(align: null, label: null, source: null, template: $template);

        $renderer = $this->createMock(TemplateRendererInterface::class);
        $renderer
            ->expects(self::once())
            ->method('render')
            ->with(
                $template,
                self::callback(fn(
                    array $ctx,
                ): bool => $ctx['currency'] === 'EUR'),
            )
            ->willReturn('EUR')
        ;

        $this->createRowCellGenerator($renderer)->generate($columnConfig, [], ['currency' => 'EUR']);
    }

    private function createRowCellGenerator(
        ?TemplateRendererInterface $renderer = null,
    ): RowCellGenerator {
        return new RowCellGenerator(
            PropertyAccess::createPropertyAccessor(),
            $renderer ?? $this->createStub(TemplateRendererInterface::class),
        );
    }
}
