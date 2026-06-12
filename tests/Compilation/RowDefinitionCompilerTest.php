<?php

declare(strict_types=1);

namespace Jmf\Grid\Tests\Compilation;

use Jmf\Grid\Compilation\AttributesCompiler;
use Jmf\Grid\Compilation\RowDefinitionCompiler;
use Override;
use PHPUnit\Framework\TestCase;

final class RowDefinitionCompilerTest extends TestCase
{
    private RowDefinitionCompiler $rowDefinitionCompiler;

    #[Override]
    protected function setUp(): void
    {
        $this->rowDefinitionCompiler = new RowDefinitionCompiler(
            new AttributesCompiler(),
        );
    }

    public function testCompileWithEmptyConfig(): void
    {
        $result = $this->rowDefinitionCompiler->compile([]);

        self::assertNull($result->getLink());
        self::assertSame([], $result->getVariables()->all());
        self::assertSame([], $result->getAttributes()->all());
    }

    public function testCompileWithLink(): void
    {
        $result = $this->rowDefinitionCompiler->compile(
            [
                'link' => 'my_route',
            ],
        );

        self::assertSame('my_route', $result->getLink());
    }

    public function testCompileWithVariables(): void
    {
        $result = $this->rowDefinitionCompiler->compile(
            [
                'variables' => [
                    'id'   => 'item.id',
                    'slug' => 'item.slug',
                ],
            ],
        );

        self::assertSame(
            [
                'id'   => 'item.id',
                'slug' => 'item.slug',
            ],
            $result->getVariables()->all(),
        );
    }

    public function testCompileWithAttributes(): void
    {
        $result = $this->rowDefinitionCompiler->compile(
            [
                'attributes' => ['class' => 'item.cssClass'],
            ],
        );

        self::assertSame(
            ['class' => 'item.cssClass'],
            $result->getAttributes()->all(),
        );
    }

    public function testCompileFullConfig(): void
    {
        $result = $this->rowDefinitionCompiler->compile(
            [
                'link'       => 'my_route',
                'variables'  => ['id' => 'item.id'],
                'attributes' => ['class' => 'item.cssClass'],
            ],
        );

        self::assertSame('my_route', $result->getLink());
        self::assertSame(['id' => 'item.id'], $result->getVariables()->all());
        self::assertSame(['class' => 'item.cssClass'], $result->getAttributes()->all());
    }
}
