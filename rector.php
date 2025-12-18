<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\ConstFetch\RemovePhpVersionIdCheckRector;
use Rector\DeadCode\Rector\If_\UnwrapFutureCompatibleIfPhpVersionRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/inc/plugins/relator.php',
    ])
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        typeDeclarations: true,
        privatization: true,
        earlyReturn: true,
        codingStyle: true,
    )
    ->withSkip([
        RemovePhpVersionIdCheckRector::class,
        UnwrapFutureCompatibleIfPhpVersionRector::class,
    ]);
