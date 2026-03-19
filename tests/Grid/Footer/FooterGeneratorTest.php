<?php

declare(strict_types=1);

namespace Jmf\Grid\Tests\Grid\Footer;

use Jmf\Grid\Configuration\Footer\FooterConfiguration;
use Jmf\Grid\Configuration\Grid\GridConfiguration;
use Jmf\Grid\Configuration\KeyValueCollection;
use Jmf\Grid\Configuration\Row\RowConfiguration;
use Jmf\Grid\Grid\Footer\FooterGenerator;
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

    public function testGenerateWithNoFooterConfigurations(): void
    {
        $gridConfiguration = $this->createGridConfiguration([]);

        $footer = $this->footerGenerator->generate($gridConfiguration, [], []);
        $rows   = iterator_to_array($footer->getRows());

        self::assertSame([], $rows);
    }

    public function testGenerateWithStaticValue(): void
    {
        $gridConfiguration = $this->createGridConfiguration(
            [
                [new FooterConfiguration(align: null, template: null, merge: null, value: 'Total', presetId: null)],
            ],
        );

        $footer = $this->footerGenerator->generate($gridConfiguration, [], []);
        $rows   = iterator_to_array($footer->getRows());

        self::assertCount(1, $rows);

        $cells = iterator_to_array($rows[0]->getCells());

        self::assertCount(1, $cells);
        self::assertSame('Total', $cells[0]->getValue());
        self::assertSame([], $cells[0]->getAttributes());
    }

    public function testGenerateWithAlignAddsClassAttribute(): void
    {
        $gridConfiguration = $this->createGridConfiguration(
            [
                [new FooterConfiguration(align: 'right', template: null, merge: null, value: 'Total', presetId: null)],
            ],
        );

        $footer = $this->footerGenerator->generate($gridConfiguration, [], []);
        $cells  = iterator_to_array(iterator_to_array($footer->getRows())[0]->getCells());

        self::assertSame(['class' => 'text-right'], $cells[0]->getAttributes());
    }

    public function testGenerateWithMergeGreaterThanOneAddsColspan(): void
    {
        $gridConfiguration = $this->createGridConfiguration(
            [
                [new FooterConfiguration(align: null, template: null, merge: 3, value: 'Total', presetId: null)],
            ],
        );

        $footer = $this->footerGenerator->generate($gridConfiguration, [], []);
        $cells  = iterator_to_array(iterator_to_array($footer->getRows())[0]->getCells());

        self::assertSame(['colspan' => 3], $cells[0]->getAttributes());
    }

    public function testGenerateWithMergeOfOneOmitsColspan(): void
    {
        $gridConfiguration = $this->createGridConfiguration(
            [
                [new FooterConfiguration(align: null, template: null, merge: 1, value: 'Total', presetId: null)],
            ],
        );

        $footer = $this->footerGenerator->generate($gridConfiguration, [], []);
        $cells  = iterator_to_array(iterator_to_array($footer->getRows())[0]->getCells());

        self::assertSame([], $cells[0]->getAttributes());
    }

    public function testGenerateWithAlignAndMerge(): void
    {
        $gridConfiguration = $this->createGridConfiguration(
            [
                [new FooterConfiguration(align: 'center', template: null, merge: 2, value: 'Total', presetId: null)],
            ],
        );

        $footer = $this->footerGenerator->generate($gridConfiguration, [], []);
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
        $template          = new StringTemplate('{{ _items|length }}');
        $gridConfiguration = $this->createGridConfiguration(
            [
                [new FooterConfiguration(align: null, template: $template, merge: null, value: null, presetId: null)],
            ],
        );

        $templateRenderer = $this->createMock(TemplateRendererInterface::class);
        $templateRenderer
            ->expects(self::once())
            ->method('render')
            ->with($template, ['_items' => []])
            ->willReturn('42')
        ;

        $footer = (new FooterGenerator($templateRenderer))->generate($gridConfiguration, [], []);
        $cells  = iterator_to_array(iterator_to_array($footer->getRows())[0]->getCells());

        self::assertSame('42', $cells[0]->getValue());
    }

    public function testGenerateWithTemplatePassesArgumentsToContext(): void
    {
        $template          = new StringTemplate('{{ currency }}');
        $gridConfiguration = $this->createGridConfiguration(
            [
                [new FooterConfiguration(align: null, template: $template, merge: null, value: null, presetId: null)],
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

        $footer = (new FooterGenerator($templateRenderer))->generate($gridConfiguration, [], ['currency' => 'EUR']);
        $cells  = iterator_to_array(iterator_to_array($footer->getRows())[0]->getCells());

        self::assertSame('EUR', $cells[0]->getValue());
    }

    public function testGenerateWithStaticValueTrimmed(): void
    {
        $gridConfiguration = $this->createGridConfiguration(
            [
                [new FooterConfiguration(align: null, template: null, merge: null, value: '  Total  ', presetId: null)],
            ],
        );

        $footer = $this->footerGenerator->generate($gridConfiguration, [], []);
        $cells  = iterator_to_array(iterator_to_array($footer->getRows())[0]->getCells());

        self::assertSame('Total', $cells[0]->getValue());
    }

    public function testGenerateWithMultipleRows(): void
    {
        $gridConfiguration = $this->createGridConfiguration(
            [
                [new FooterConfiguration(align: null, template: null, merge: null, value: 'Subtotal', presetId: null)],
                [new FooterConfiguration(align: null, template: null, merge: null, value: 'Total', presetId: null)],
            ],
        );

        $footer = $this->footerGenerator->generate($gridConfiguration, [], []);
        $rows   = iterator_to_array($footer->getRows());

        self::assertCount(2, $rows);
        self::assertSame('Subtotal', iterator_to_array($rows[0]->getCells())[0]->getValue());
        self::assertSame('Total', iterator_to_array($rows[1]->getCells())[0]->getValue());
    }

    public function testGenerateWithMultipleCellsInRow(): void
    {
        $gridConfiguration = $this->createGridConfiguration(
            [
                [
                    new FooterConfiguration(align: null, template: null, merge: null, value: 'Label', presetId: null),
                    new FooterConfiguration(
                        align: 'right', template: null, merge: null, value: '100.00', presetId: null,
                    ),
                ],
            ],
        );

        $footer = $this->footerGenerator->generate($gridConfiguration, [], []);
        $cells  = iterator_to_array(iterator_to_array($footer->getRows())[0]->getCells());

        self::assertCount(2, $cells);
        self::assertSame('Label', $cells[0]->getValue());
        self::assertSame('100.00', $cells[1]->getValue());
        self::assertSame(['class' => 'text-right'], $cells[1]->getAttributes());
    }

    /**
     * @param FooterConfiguration[][] $footerConfigurations
     */
    private function createGridConfiguration(iterable $footerConfigurations): GridConfiguration
    {
        return new GridConfiguration(
            id:                   'test',
            arguments:            [],
            gridVariables:        KeyValueCollection::createEmpty(),
            columnConfigurations: [],
            rowConfiguration:     RowConfiguration::createEmpty(),
            footerConfigurations: $footerConfigurations,
        );
    }
}
