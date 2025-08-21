<?php

declare(strict_types=1);

namespace Jmf\Grid\Grid;

use Jmf\Grid\Configuration\Grid\GridConfiguration;
use Jmf\Grid\Configuration\Grid\GridConfigurationLoader;
use Jmf\Grid\Configuration\Grid\GridConfigurationRepositoryInterface;
use Jmf\Grid\Exception\GridNotFoundException;
use Jmf\Grid\Exception\GridWithoutColumnException;
use Jmf\Grid\Exception\MissingGridArgumentException;
use Jmf\Grid\Exception\UnexpectedValueTypeException;
use Jmf\Grid\Grid\Column\ColumnCollection;
use Jmf\Grid\Grid\Column\ColumnCollectionGenerator;
use Jmf\Grid\Grid\Footer\Footer;
use Jmf\Grid\Grid\Footer\FooterGenerator;
use Jmf\Grid\Grid\Row\RowCollection;
use Jmf\Grid\Grid\Row\RowCollectionGenerator;
use Jmf\RenderingPreset\Exception\InvalidConfigurationException;
use Jmf\RenderingPreset\Exception\PresetNotFoundException;
use Jmf\TemplateRendering\Exception\TemplateRenderingException;

readonly class GridGenerator
{
    public function __construct(
        private GridConfigurationRepositoryInterface $gridConfigurationRepository,
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
        $gridConfiguration = $this->gridConfigurationRepository->getCollection()->get($gridId);

        $this->validateArguments(
            $gridId,
            $arguments,
            $gridConfiguration,
        );

        return new Grid(
            $this->generateColumns($gridConfiguration),
            $this->generateRows($gridConfiguration, $arguments, $items),
            $this->generateFooter($gridConfiguration, $arguments, $items),
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
        GridConfiguration $gridConfiguration,
    ): void {
        foreach ($gridConfiguration->getArguments() as $argument) {
            if (!array_key_exists($argument, $arguments)) {
                throw new MissingGridArgumentException(
                    $gridId,
                    $argument,
                );
            }
        }
    }

    private function generateColumns(GridConfiguration $gridConfiguration): ColumnCollection
    {
        return $this->columnCollectionGenerator->generate(
            $gridConfiguration,
        );
    }

    /**
     * @param array<string, mixed>              $arguments
     * @param list<array<string, mixed>|object> $items
     *
     * @throws TemplateRenderingException
     * @throws UnexpectedValueTypeException
     */
    private function generateRows(
        GridConfiguration $gridConfiguration,
        array $arguments,
        array $items,
    ): RowCollection {
        return $this->rowCollectionGenerator->generate(
            $gridConfiguration,
            $items,
            $arguments,
        );
    }

    /**
     * @param array<string, mixed>              $arguments
     * @param list<array<string, mixed>|object> $items
     *
     * @throws TemplateRenderingException
     */
    private function generateFooter(
        GridConfiguration $gridConfiguration,
        array $arguments,
        array $items,
    ): Footer {
        return $this->footerGenerator->generate(
            $gridConfiguration,
            $items,
            $arguments,
        );
    }
}
