<?php declare(strict_types=1);

namespace MichaelCozzolino\PsalmHtmlReport;

use MichaelCozzolino\PsalmHtmlReport\VO\Issue;
use MichaelCozzolino\PsalmHtmlReport\VO\Report;
use Psalm\Internal\Analyzer\IssueData;
use Psalm\Plugin\EventHandler\AfterAnalysisInterface;
use Psalm\Plugin\EventHandler\Event\AfterAnalysisEvent;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use function file_put_contents;
use function trim;

/**
 * @psalm-suppress InternalProperty It's given by the api
 * @psalm-suppress InternalMethod It's given by the api
 */
class HtmlReportGenerator implements AfterAnalysisInterface
{
    public static function afterAnalysis(AfterAnalysisEvent $event): void
    {
        $codebase    = $event->getCodebase();
        $psalmIssues = $event->getIssues();
        $twig        = new Environment(new FilesystemLoader('src'));

        $totalIssues = 0;
        $issues      = [];

        /** @var list<IssueData> $fileIssues */
        foreach ($psalmIssues as $filePath => $fileIssues) {
            foreach ($fileIssues as $issue) {
                $issues[$filePath][] = new Issue(
                    $issue->message,
                    $issue->severity,
                    $issue->type,
                    trim($issue->snippet),
                    $issue->line_from,
                    $issue->link
                );

                $totalIssues += 1;
            }
        }

        $htmlReport = $twig->render(
            'index.html.twig',
            [
                'report' => new Report(
                    $codebase->config->base_dir,
                    $codebase->analyzer->getTypeInferenceSummary($codebase),
                    $issues,
                    $totalIssues
                ),
            ]
        );

        file_put_contents('src/index.html', $htmlReport);
    }
}
