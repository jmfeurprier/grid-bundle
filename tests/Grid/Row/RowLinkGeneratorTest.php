<?php

declare(strict_types=1);

namespace Jmf\Grid\Tests\Grid\Row;

use Jmf\Grid\Configuration\Grid\GridConfiguration;
use Jmf\Grid\Configuration\KeyValueCollection;
use Jmf\Grid\Configuration\Row\RowConfiguration;
use Jmf\Grid\Grid\Row\RowLinkGenerator;
use Jmf\TemplateRendering\TemplateRendererInterface;
use PHPUnit\Framework\TestCase;

final class RowLinkGeneratorTest extends TestCase
{
    /**
     * @param string[] $arguments
     */
    private function createGridConfiguration(?string $link, array $arguments = []): GridConfiguration
    {
        return new GridConfiguration(
            id:                   'test',
            arguments:            $arguments,
            gridVariables:        KeyValueCollection::createEmpty(),
            columnConfigurations: [],
            rowConfiguration:     new RowConfiguration($link, KeyValueCollection::createEmpty(), KeyValueCollection::createEmpty()),
            footerConfigurations: [],
        );
    }

    public function testGenerateWithNoLinkReturnsNull(): void
    {
        $generator         = new RowLinkGenerator($this->createStub(TemplateRendererInterface::class));
        $gridConfiguration = $this->createGridConfiguration(null);

        $result = $generator->generate($gridConfiguration, [], [], []);

        self::assertNull($result);
    }

    public function testGenerateWithLinkRendersTemplate(): void
    {
        $renderer = $this->createMock(TemplateRendererInterface::class);
        $renderer
            ->expects(self::once())
            ->method('renderFromString')
            ->with('/items/{{ _item.id }}', self::anything())
            ->willReturn('/items/42');

        $generator         = new RowLinkGenerator($renderer);
        $gridConfiguration = $this->createGridConfiguration('/items/{{ _item.id }}');

        $result = $generator->generate($gridConfiguration, ['id' => 42], [], []);

        self::assertSame('/items/42', $result);
    }

    public function testGenerateWithLinkMergesArgumentsAndRowVariables(): void
    {
        $renderer = $this->createMock(TemplateRendererInterface::class);
        $renderer
            ->expects(self::once())
            ->method('renderFromString')
            ->with(
                '/items/{{ _item.id }}',
                self::callback(
                    fn (array $ctx) => isset($ctx['_item'], $ctx['locale'], $ctx['myVar'])
                        && $ctx['locale'] === 'fr'
                        && $ctx['myVar'] === 'hello',
                ),
            )
            ->willReturn('/items/1');

        $generator         = new RowLinkGenerator($renderer);
        $gridConfiguration = $this->createGridConfiguration('/items/{{ _item.id }}');

        $generator->generate(
            $gridConfiguration,
            ['id' => 1],
            ['myVar' => 'hello'],
            ['locale' => 'fr'],
        );
    }
}
