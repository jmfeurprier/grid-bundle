<?php

declare(strict_types=1);

namespace Jmf\Grid\Tests\Grid\Row;

use Jmf\Grid\Grid\Row\AttributesLoader;
use Jmf\Grid\Grid\Row\RowDefinitionLoader;
use Override;
use PHPUnit\Framework\TestCase;

final class RowDefinitionLoaderTest extends TestCase
{
    private RowDefinitionLoader $loader;

    #[Override]
    protected function setUp(): void
    {
        $this->loader = new RowDefinitionLoader(
            new AttributesLoader(),
        );
    }

    public function testLoadWithEmptyConfig(): void
    {
        $result = $this->loader->load([]);

        self::assertNull($result->getLink());
        self::assertSame([], $result->getVariables()->all());
        self::assertSame([], $result->getAttributes()->all());
    }

    public function testLoadWithLink(): void
    {
        $result = $this->loader->load(['link' => 'my_route']);

        self::assertSame('my_route', $result->getLink());
    }

    public function testLoadWithVariables(): void
    {
        $result = $this->loader->load([
            'variables' => ['id' => 'item.id', 'slug' => 'item.slug'],
        ]);

        self::assertSame(
            ['id' => 'item.id', 'slug' => 'item.slug'],
            $result->getVariables()->all(),
        );
    }

    public function testLoadWithAttributes(): void
    {
        $result = $this->loader->load([
            'attributes' => ['class' => 'item.cssClass'],
        ]);

        self::assertSame(
            ['class' => 'item.cssClass'],
            $result->getAttributes()->all(),
        );
    }

    public function testLoadFullConfig(): void
    {
        $result = $this->loader->load([
            'link'       => 'my_route',
            'variables'  => ['id' => 'item.id'],
            'attributes' => ['class' => 'item.cssClass'],
        ]);

        self::assertSame('my_route', $result->getLink());
        self::assertSame(['id' => 'item.id'], $result->getVariables()->all());
        self::assertSame(['class' => 'item.cssClass'], $result->getAttributes()->all());
    }
}
