<?php

declare(strict_types=1);

namespace Jmf\Grid\Tests\Grid;

use Jmf\Grid\Configuration\Grid\GridConfiguration;
use Jmf\Grid\Configuration\Grid\GridConfigurationCollection;
use Jmf\Grid\Configuration\Grid\GridConfigurationRepositoryInterface;
use Jmf\Grid\Configuration\KeyValueCollection;
use Jmf\Grid\Configuration\Row\RowConfiguration;
use Jmf\Grid\Exception\GridNotFoundException;
use Jmf\Grid\Exception\MissingGridArgumentException;
use Jmf\Grid\Grid\Column\ColumnCollection;
use Jmf\Grid\Grid\Column\ColumnCollectionGenerator;
use Jmf\Grid\Grid\Footer\Footer;
use Jmf\Grid\Grid\Footer\FooterGenerator;
use Jmf\Grid\Grid\Grid;
use Jmf\Grid\Grid\GridGenerator;
use Jmf\Grid\Grid\Row\RowCollection;
use Jmf\Grid\Grid\Row\RowCollectionGenerator;
use Override;
use PHPUnit\Framework\TestCase;

final class GridGeneratorTest extends TestCase
{
    private ColumnCollectionGenerator $columnCollectionGenerator;

    private RowCollectionGenerator $rowCollectionGenerator;

    private FooterGenerator $footerGenerator;

    #[Override]
    protected function setUp(): void
    {
        $this->columnCollectionGenerator = $this->createStub(ColumnCollectionGenerator::class);
        $this->columnCollectionGenerator->method('generate')->willReturn(new ColumnCollection([]));

        $this->rowCollectionGenerator = $this->createStub(RowCollectionGenerator::class);
        $this->rowCollectionGenerator->method('generate')->willReturn(new RowCollection([]));

        $this->footerGenerator = $this->createStub(FooterGenerator::class);
        $this->footerGenerator->method('generate')->willReturn(new Footer([]));
    }

    public function testGenerateReturnsGrid(): void
    {
        $gridConfig                  = $this->createGridConfiguration('myGrid');
        $gridConfigurationRepository = $this->createGridConfigurationRepository($gridConfig);

        $gridGenerator = new GridGenerator(
            $gridConfigurationRepository,
            $this->columnCollectionGenerator,
            $this->rowCollectionGenerator,
            $this->footerGenerator,
        );

        $result = $gridGenerator->generate('myGrid', [], []);

        self::assertCount(0, $result->getColumns());
        self::assertCount(0, $result->getRows());
        self::assertCount(0, $result->getFooter()->getRows());
    }

    public function testGenerateThrowsGridNotFoundException(): void
    {
        $gridConfigurationRepository = $this->createGridConfigurationRepository();

        $gridGenerator = new GridGenerator(
            $gridConfigurationRepository,
            $this->columnCollectionGenerator,
            $this->rowCollectionGenerator,
            $this->footerGenerator,
        );

        $this->expectException(GridNotFoundException::class);

        $gridGenerator->generate('unknown', [], []);
    }

    public function testGenerateThrowsMissingGridArgumentException(): void
    {
        $gridConfig                  = $this->createGridConfiguration('myGrid', ['locale']);
        $gridConfigurationRepository = $this->createGridConfigurationRepository($gridConfig);

        $gridGenerator = new GridGenerator(
            $gridConfigurationRepository,
            $this->columnCollectionGenerator,
            $this->rowCollectionGenerator,
            $this->footerGenerator,
        );

        $this->expectException(MissingGridArgumentException::class);

        $gridGenerator->generate('myGrid', [], []);
    }

    public function testGenerateWithAllRequiredArgumentsSucceeds(): void
    {
        $gridConfig                  = $this->createGridConfiguration('myGrid', ['locale']);
        $gridConfigurationRepository = $this->createGridConfigurationRepository($gridConfig);

        $gridGenerator = new GridGenerator(
            $gridConfigurationRepository,
            $this->columnCollectionGenerator,
            $this->rowCollectionGenerator,
            $this->footerGenerator,
        );

        $result = $gridGenerator->generate('myGrid', [], ['locale' => 'fr']);

        self::assertInstanceOf(Grid::class, $result);
    }

    /**
     * @param non-empty-string $id
     * @param string[]         $requiredArguments
     */
    private function createGridConfiguration(
        string $id,
        array $requiredArguments = [],
    ): GridConfiguration {
        return new GridConfiguration(
            id:                   $id,
            arguments:            $requiredArguments,
            gridVariables:        KeyValueCollection::createEmpty(),
            columnConfigurations: [],
            rowConfiguration:     RowConfiguration::createEmpty(),
            footerConfigurations: [],
        );
    }

    private function createGridConfigurationRepository(
        GridConfiguration ...$configs,
    ): GridConfigurationRepositoryInterface {
        $gridConfigurationCollection = new GridConfigurationCollection($configs);

        $gridConfigurationRepository = $this->createStub(GridConfigurationRepositoryInterface::class);
        $gridConfigurationRepository->method('getCollection')->willReturn($gridConfigurationCollection);

        return $gridConfigurationRepository;
    }
}
