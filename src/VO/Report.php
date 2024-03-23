<?php declare(strict_types=1);

namespace MichaelCozzolino\PsalmHtmlReport\VO;

use JetBrains\PhpStorm\Immutable;

#[Immutable]
class Report
{
    /**
     * @param string                     $projectDirectory
     * @param string                     $typeInferenceSummary
     * @param array<string, list<Issue>> $issues The key is the file path
     * @param non-negative-int           $totalIssues
     */
    public function __construct(
        public readonly string $projectDirectory,
        public readonly string $typeInferenceSummary,
        public readonly array  $issues,
        public readonly int    $totalIssues
    ) {
    }
}
