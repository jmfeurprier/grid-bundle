<?php

declare(strict_types=1);

namespace Jmf\Grid\Tests\Generation;

use Jmf\Grid\Definition\GridDefinition;
use Jmf\Grid\Definition\GridDefinitionCollection;
use Jmf\Grid\Definition\RowDefinition;
use Jmf\Grid\Exception\GridNotFoundException;
use Jmf\Grid\Exception\MissingGridArgumentException;
use Jmf\Grid\Generation\ColumnCollectionGenerator;
use Jmf\Grid\Generation\FooterGenerator;
use Jmf\Grid\Generation\GridGenerator;
use Jmf\Grid\Generation\RowCollectionGenerator;
use Jmf\Grid\Model\ColumnCollection;
use Jmf\Grid\Model\Footer;
use Jmf\Grid\Model\Grid;
use Jmf\Grid\Definition\KeyValueCollection;
use Jmf\Grid\Model\RowCollection;
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
        $gridConfig    = $this->createGridDefinition('myGrid');
        $gridGenerator = $this->createGridGenerator($gridConfig);

        $result = $gridGenerator->generate('myGrid', [], []);

        self::assertCount(0, $result->getColumns());
        self::assertCount(0, $result->getRows());
        self::assertCount(0, $result->getFooter()->getRows());
    }

    public function testGenerateThrowsGridNotFoundException(): void
    {
        $gridGenerator = $this->createGridGenerator();

        $this->expectException(GridNotFoundException::class);

        $gridGenerator->generate('unknown', [], []);
    }

    public function testGenerateThrowsMissingGridArgumentException(): void
    {
        $gridConfig    = $this->createGridDefinition('myGrid', ['locale']);
        $gridGenerator = $this->createGridGenerator($gridConfig);

        $this->expectException(MissingGridArgumentException::class);

        $gridGenerator->generate('myGrid', [], []);
    }

    public function testGenerateWithAllRequiredArgumentsSucceeds(): void
    {
        $gridConfig    = $this->createGridDefinition('myGrid', ['locale']);
        $gridGenerator = $this->createGridGenerator($gridConfig);

        $result = $gridGenerator->generate('myGrid', [], ['locale' => 'fr']);

        self::assertInstanceOf(Grid::class, $result);
    }

    /**
     * @param non-empty-string $id
     * @param string[]         $requiredArguments
     */
    private function createGridDefinition(
        string $id,
        array $requiredArguments = [],
    ): GridDefinition {
        return new GridDefinition(
            id:                   $id,
            arguments:            $requiredArguments,
            gridVariables:        KeyValueCollection::createEmpty(),
            columnDefinitions: [],
            rowDefinition:     RowDefinition::createEmpty(),
            footerDefinitions: [],
        );
    }

    private function createGridGenerator(GridDefinition ...$configs): GridGenerator
    {
        return new GridGenerator(
            new GridDefinitionCollection($configs),
            $this->columnCollectionGenerator,
            $this->rowCollectionGenerator,
            $this->footerGenerator,
        );
    }
}
