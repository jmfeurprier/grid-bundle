<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\MethodCall\RemoveNullArgOnNullDefaultParamRector;
use Rector\Naming\Rector\Assign\RenameVariableToMatchMethodCallReturnTypeRector;
use Rector\Naming\Rector\Class_\RenamePropertyToMatchTypeRector;
use Rector\Naming\Rector\ClassMethod\RenameParamToMatchTypeRector;
use Rector\Naming\Rector\ClassMethod\RenameVariableToMatchNewTypeRector;
use Rector\Naming\Rector\Foreach_\RenameForeachValueVariableToMatchExprVariableRector;
use Rector\Naming\Rector\Foreach_\RenameForeachValueVariableToMatchMethodCallReturnTypeRector;
use Rector\PHPUnit\CodeQuality\Rector\Class_\AddSeeTestAnnotationRector;
use Rector\PHPUnit\CodeQuality\Rector\Class_\PreferPHPUnitThisCallRector;

$rootPath = realpath(__DIR__ . '/..') . '/';

return RectorConfig::configure()
    ->withCache($rootPath . 'var/cache')
    ->withPaths(
        [
            $rootPath . 'config',
            $rootPath . 'src',
            $rootPath . 'tests',
        ],
    )
    ->withPhpSets()
    ->withSkip(
        [
            PreferPHPUnitThisCallRector::class,
            RemoveNullArgOnNullDefaultParamRector::class,
            RenameForeachValueVariableToMatchExprVariableRector::class,
            RenameForeachValueVariableToMatchMethodCallReturnTypeRector::class,
            RenameParamToMatchTypeRector::class,
            RenamePropertyToMatchTypeRector::class,
            RenameVariableToMatchMethodCallReturnTypeRector::class,
            RenameVariableToMatchNewTypeRector::class,
            AddSeeTestAnnotationRector::class,
        ],
    )
    ->withPreparedSets(
        deadCode:                 true,
        codeQuality:              true,
        codingStyle:              true,
        typeDeclarations:         true,
        typeDeclarationDocblocks: true,
        privatization:            true,
        naming:                   true,
        instanceOf:               true,
        earlyReturn:              true,
        carbon:                   true,
        rectorPreset:             true,
        phpunitCodeQuality:       true,
        doctrineCodeQuality:      true,
        symfonyCodeQuality:       true,
        symfonyConfigs:           true,
    )
;
