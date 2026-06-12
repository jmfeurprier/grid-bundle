<?php

declare(strict_types=1);

namespace Jmf\Grid\Tests\Grid\Row;

use Jmf\Grid\Grid\GridDefinition;
use Jmf\Grid\Grid\KeyValueCollection;
use Jmf\Grid\Grid\Row\RowDefinition;
use Jmf\Grid\Grid\Row\RowLinkGenerator;
use Jmf\TemplateRendering\TemplateRendererInterface;
use PHPUnit\Framework\TestCase;

final class RowLinkGeneratorTest extends TestCase
{
    /**
     * @param string[] $arguments
     */
    private function createGridDefinition(
        ?string $link,
        array $arguments = [],
    ): GridDefinition {
        return new GridDefinition(
            id:                   'test',
            arguments:            $arguments,
            gridVariables:        KeyValueCollection::createEmpty(),
            columnDefinitions: [],
            rowDefinition:     new RowDefinition(
                                      $link,
                                      KeyValueCollection::createEmpty(),
                                      KeyValueCollection::createEmpty(),
                                  ),
            footerDefinitions: [],
        );
    }

    public function testGenerateWithNoLinkReturnsNull(): void
    {
        $generator         = new RowLinkGenerator(
            $this->createStub(TemplateRendererInterface::class),
        );
        $gridDefinition = $this->createGridDefinition(null);

        $result = $generator->generate($gridDefinition, []);

        self::assertNull($result);
    }

    public function testGenerateWithLinkRendersTemplate(): void
    {
        $renderer = $this->createMock(TemplateRendererInterface::class);
        $renderer
            ->expects(self::once())
            ->method('renderFromString')
            ->with('/items/{{ _item.id }}', self::anything())
            ->willReturn('/items/42')
        ;

        $generator         = new RowLinkGenerator($renderer);
        $gridDefinition = $this->createGridDefinition('/items/{{ _item.id }}');

        $result = $generator->generate($gridDefinition, ['_item' => ['id' => 42]]);

        self::assertSame('/items/42', $result);
    }

    public function testGeneratePassesRowVariablesAsContext(): void
    {
        $item         = ['id' => 1];
        $rowVariables = [
            '_item'  => $item,
            'myVar'  => 'hello',
            'locale' => 'fr',
        ];

        $renderer = $this->createMock(TemplateRendererInterface::class);
        $renderer
            ->expects(self::once())
            ->method('renderFromString')
            ->with(
                '/items/{{ _item.id }}',
                self::callback(
                    fn(
                        array $ctx,
                    ): bool => $ctx === $rowVariables,
                ),
            )
            ->willReturn('/items/1')
        ;

        $generator         = new RowLinkGenerator($renderer);
        $gridDefinition = $this->createGridDefinition('/items/{{ _item.id }}');

        $generator->generate($gridDefinition, $rowVariables);
    }

    public function testRowVariablesTakePriorityOverNothingElse(): void
    {
        $rowVariables = ['key' => 'from_row_variables'];
        $renderer     = $this->createMock(TemplateRendererInterface::class);
        $renderer
            ->expects(self::once())
            ->method('renderFromString')
            ->with(
                self::anything(),
                self::callback(fn(array $ctx): bool => $ctx['key'] === 'from_row_variables'),
            )
            ->willReturn('/link')
        ;

        $generator         = new RowLinkGenerator($renderer);
        $gridDefinition = $this->createGridDefinition('/link');

        $generator->generate($gridDefinition, $rowVariables);
    }
}
