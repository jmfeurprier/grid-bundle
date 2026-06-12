<?php

declare(strict_types=1);

namespace Jmf\Grid\Generation;

use Jmf\Grid\Definition\GridDefinition;
use Jmf\Grid\Definition\GridDefinitionCollection;
use Jmf\Grid\Exception\GridNotFoundException;
use Jmf\Grid\Exception\GridWithoutColumnException;
use Jmf\Grid\Exception\MissingGridArgumentException;
use Jmf\Grid\Exception\UnexpectedValueTypeException;
use Jmf\Grid\Model\ColumnCollection;
use Jmf\Grid\Model\Footer;
use Jmf\Grid\Model\Grid;
use Jmf\Grid\Model\RowCollection;
use Jmf\RenderingPreset\Exception\InvalidConfigurationException;
use Jmf\RenderingPreset\Exception\PresetNotFoundException;
use Jmf\TemplateRendering\Exception\TemplateRenderingException;

readonly class GridGenerator
{
    public function __construct(
        private GridDefinitionCollection $gridDefinitionCollection,
        private ColumnCollectionGenerator $columnCollectionGenerator,
        private RowCollectionGenerator $rowCollectionGenerator,
        private FooterGenerator $footerGenerator,
    ) {
    }

    /**
     * @param non-empty-string                  $gridId
     * @param list<array<string, mixed>|object> $items
     * @param array<string, mixed>              $arguments
     *
     * @throws GridNotFoundException
     * @throws GridWithoutColumnException
     * @throws InvalidConfigurationException
     * @throws MissingGridArgumentException
     * @throws PresetNotFoundException
     * @throws TemplateRenderingException
     * @throws UnexpectedValueTypeException
     */
    public function generate(
        string $gridId,
        array $items,
        array $arguments,
    ): Grid {
        $gridDefinition = $this->gridDefinitionCollection->get($gridId);

        $this->validateArguments(
            $gridId,
            $arguments,
            $gridDefinition,
        );

        return new Grid(
            $this->generateColumns($gridDefinition),
            $this->generateRows($gridDefinition, $items, $arguments),
            $this->generateFooter($gridDefinition, $items, $arguments),
        );
    }

    /**
     * @param non-empty-string     $gridId
     * @param array<string, mixed> $arguments
     *
     * @throws MissingGridArgumentException
     */
    private function validateArguments(
        string $gridId,
        array $arguments,
        GridDefinition $gridDefinition,
    ): void {
        foreach ($gridDefinition->getArguments() as $argument) {
            if (!array_key_exists($argument, $arguments)) {
                throw new MissingGridArgumentException(
                    $gridId,
                    $argument,
                );
            }
        }
    }

    private function generateColumns(GridDefinition $gridDefinition): ColumnCollection
    {
        return $this->columnCollectionGenerator->generate(
            $gridDefinition,
        );
    }

    /**
     * @param list<array<string, mixed>|object> $items
     * @param array<string, mixed>              $arguments
     *
     * @throws TemplateRenderingException
     * @throws UnexpectedValueTypeException
     */
    private function generateRows(
        GridDefinition $gridDefinition,
        array $items,
        array $arguments,
    ): RowCollection {
        return $this->rowCollectionGenerator->generate(
            $gridDefinition,
            $items,
            $arguments,
        );
    }

    /**
     * @param list<array<string, mixed>|object> $items
     * @param array<string, mixed>              $arguments
     *
     * @throws TemplateRenderingException
     */
    private function generateFooter(
        GridDefinition $gridDefinition,
        array $items,
        array $arguments,
    ): Footer {
        return $this->footerGenerator->generate(
            $gridDefinition,
            $items,
            $arguments,
        );
    }
}
