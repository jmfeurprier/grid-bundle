<?php

declare(strict_types=1);

namespace Jmf\Grid\Tests\Generation;

use Jmf\Grid\Definition\FooterDefinition;
use Jmf\Grid\Definition\GridDefinition;
use Jmf\Grid\Definition\RowDefinition;
use Jmf\Grid\Generation\FooterGenerator;
use Jmf\Grid\Definition\KeyValueCollection;
use Jmf\TemplateRendering\StringTemplate;
use Jmf\TemplateRendering\TemplateRendererInterface;
use Override;
use PHPUnit\Framework\TestCase;

final class FooterGeneratorTest extends TestCase
{
    private FooterGenerator $footerGenerator;

    #[Override]
    protected function setUp(): void
    {
        $this->footerGenerator = new FooterGenerator(
            $this->createStub(TemplateRendererInterface::class),
        );
    }

    public function testGenerateWithNoFooterDefinitions(): void
    {
        $gridDefinition = $this->createGridDefinition([]);

        $footer = $this->footerGenerator->generate($gridDefinition, [], []);
        $rows   = iterator_to_array($footer->getRows());

        self::assertSame([], $rows);
    }

    public function testGenerateWithStaticValue(): void
    {
        $gridDefinition = $this->createGridDefinition(
            [
                [
                    new FooterDefinition(
                        align:    null,
                        template: null,
                        merge:    null,
                        value:    'Total',
                        presetId: null,
                    ),
                ],
            ],
        );

        $footer = $this->footerGenerator->generate($gridDefinition, [], []);
        $rows   = iterator_to_array($footer->getRows());

        self::assertCount(1, $rows);

        $cells = iterator_to_array($rows[0]->getCells());

        self::assertCount(1, $cells);
        self::assertSame('Total', $cells[0]->getValue());
        self::assertSame([], $cells[0]->getAttributes());
    }

    public function testGenerateWithAlignAddsClassAttribute(): void
    {
        $gridDefinition = $this->createGridDefinition(
            [
                [
                    new FooterDefinition(
                        align:    'right',
                        template: null,
                        merge:    null,
                        value:    'Total',
                        presetId: null,
                    ),
                ],
            ],
        );

        $footer = $this->footerGenerator->generate($gridDefinition, [], []);
        $cells  = iterator_to_array(iterator_to_array($footer->getRows())[0]->getCells());

        self::assertSame(['class' => 'text-right'], $cells[0]->getAttributes());
    }

    public function testGenerateWithMergeGreaterThanOneAddsColspan(): void
    {
        $gridDefinition = $this->createGridDefinition(
            [
                [
                    new FooterDefinition(
                        align:    null,
                        template: null,
                        merge:    3,
                        value:    'Total',
                        presetId: null,
                    ),
                ],
            ],
        );

        $footer = $this->footerGenerator->generate($gridDefinition, [], []);
        $cells  = iterator_to_array(iterator_to_array($footer->getRows())[0]->getCells());

        self::assertSame(['colspan' => 3], $cells[0]->getAttributes());
    }

    public function testGenerateWithMergeOfOneOmitsColspan(): void
    {
        $gridDefinition = $this->createGridDefinition(
            [
                [
                    new FooterDefinition(
                        align:    null,
                        template: null,
                        merge:    1,
                        value:    'Total',
                        presetId: null,
                    ),
                ],
            ],
        );

        $footer = $this->footerGenerator->generate($gridDefinition, [], []);
        $cells  = iterator_to_array(iterator_to_array($footer->getRows())[0]->getCells());

        self::assertSame([], $cells[0]->getAttributes());
    }

    public function testGenerateWithAlignAndMerge(): void
    {
        $gridDefinition = $this->createGridDefinition(
            [
                [
                    new FooterDefinition(
                        align:    'center',
                        template: null,
                        merge:    2,
                        value:    'Total',
                        presetId: null,
                    ),
                ],
            ],
        );

        $footer = $this->footerGenerator->generate($gridDefinition, [], []);
        $cells  = iterator_to_array(iterator_to_array($footer->getRows())[0]->getCells());

        self::assertSame(
            [
                'class'   => 'text-center',
                'colspan' => 2,
            ],
            $cells[0]->getAttributes(),
        );
    }

    public function testGenerateWithTemplate(): void
    {
        $template       = new StringTemplate('{{ _items|length }}');
        $gridDefinition = $this->createGridDefinition(
            [
                [
                    new FooterDefinition(
                        align:    null,
                        template: $template,
                        merge:    null,
                        value:    null,
                        presetId: null,
                    ),
                ],
            ],
        );

        $templateRenderer = $this->createMock(TemplateRendererInterface::class);
        $templateRenderer
            ->expects(self::once())
            ->method('render')
            ->with($template, ['_items' => []])
            ->willReturn('42')
        ;

        $footer = (new FooterGenerator($templateRenderer))->generate($gridDefinition, [], []);
        $cells  = iterator_to_array(iterator_to_array($footer->getRows())[0]->getCells());

        self::assertSame('42', $cells[0]->getValue());
    }

    public function testGenerateWithTemplatePassesArgumentsToContext(): void
    {
        $template       = new StringTemplate('{{ currency }}');
        $gridDefinition = $this->createGridDefinition(
            [
                [
                    new FooterDefinition(
                        align:    null,
                        template: $template,
                        merge:    null,
                        value:    null,
                        presetId: null,
                    ),
                ],
            ],
        );

        $templateRenderer = $this->createMock(TemplateRendererInterface::class);
        $templateRenderer
            ->expects(self::once())
            ->method('render')
            ->with(
                $template,
                [
                    'currency' => 'EUR',
                    '_items'   => [],
                ],
            )
            ->willReturn('EUR')
        ;

        $footer = (new FooterGenerator($templateRenderer))->generate($gridDefinition, [], ['currency' => 'EUR']);
        $cells  = iterator_to_array(iterator_to_array($footer->getRows())[0]->getCells());

        self::assertSame('EUR', $cells[0]->getValue());
    }

    public function testGenerateWithStaticValueTrimmed(): void
    {
        $gridDefinition = $this->createGridDefinition(
            [
                [
                    new FooterDefinition(
                        align:    null,
                        template: null,
                        merge:    null,
                        value:    '  Total  ',
                        presetId: null,
                    ),
                ],
            ],
        );

        $footer = $this->footerGenerator->generate($gridDefinition, [], []);
        $cells  = iterator_to_array(iterator_to_array($footer->getRows())[0]->getCells());

        self::assertSame('Total', $cells[0]->getValue());
    }

    public function testGenerateWithMultipleRows(): void
    {
        $gridDefinition = $this->createGridDefinition(
            [
                [
                    new FooterDefinition(
                        align:    null,
                        template: null,
                        merge:    null,
                        value:    'Subtotal',
                        presetId: null,
                    ),
                ],
                [
                    new FooterDefinition(
                        align:    null,
                        template: null,
                        merge:    null,
                        value:    'Total',
                        presetId: null,
                    ),
                ],
            ],
        );

        $footer = $this->footerGenerator->generate($gridDefinition, [], []);
        $rows   = iterator_to_array($footer->getRows());

        self::assertCount(2, $rows);
        self::assertSame('Subtotal', iterator_to_array($rows[0]->getCells())[0]->getValue());
        self::assertSame('Total', iterator_to_array($rows[1]->getCells())[0]->getValue());
    }

    public function testGenerateWithMultipleCellsInRow(): void
    {
        $gridDefinition = $this->createGridDefinition(
            [
                [
                    new FooterDefinition(
                        align:    null,
                        template: null,
                        merge:    null,
                        value:    'Label',
                        presetId: null,
                    ),
                    new FooterDefinition(
                        align:    'right',
                        template: null,
                        merge:    null,
                        value:    '100.00',
                        presetId: null,
                    ),
                ],
            ],
        );

        $footer = $this->footerGenerator->generate($gridDefinition, [], []);
        $cells  = iterator_to_array(iterator_to_array($footer->getRows())[0]->getCells());

        self::assertCount(2, $cells);
        self::assertSame('Label', $cells[0]->getValue());
        self::assertSame('100.00', $cells[1]->getValue());
        self::assertSame(['class' => 'text-right'], $cells[1]->getAttributes());
    }

    /**
     * @param FooterDefinition[][] $footerDefinitions
     */
    private function createGridDefinition(iterable $footerDefinitions): GridDefinition
    {
        return new GridDefinition(
            id:                'test',
            arguments:         [],
            gridVariables:     KeyValueCollection::createEmpty(),
            columnDefinitions: [],
            rowDefinition:     RowDefinition::createEmpty(),
            footerDefinitions: $footerDefinitions,
        );
    }
}
