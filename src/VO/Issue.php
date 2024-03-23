<?php declare(strict_types=1);

namespace MichaelCozzolino\PsalmHtmlReport\VO;

use JetBrains\PhpStorm\Immutable;

#[Immutable]
class Issue
{
    public function __construct(
        public readonly string $message,
        public readonly string $severity,
        public readonly string $type,
        public readonly string $snippet,
        public readonly int    $startLine,
        public readonly string $docsUrl
    ) {

    }
}
