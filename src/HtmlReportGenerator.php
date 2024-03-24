<?php declare(strict_types=1);

namespace MichaelCozzolino\PsalmHtmlReport;

use MichaelCozzolino\PsalmHtmlReport\VO\Issue;
use MichaelCozzolino\PsalmHtmlReport\VO\Report;
use Psalm\Internal\Analyzer\IssueData;
use Psalm\Plugin\EventHandler\AfterAnalysisInterface;
use Psalm\Plugin\EventHandler\Event\AfterAnalysisEvent;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;
use function array_key_exists;
use function file_put_contents;
use function getopt;
use function trim;
use const PHP_EOL;

/**
 * @psalm-suppress InternalProperty It's given by the api
 * @psalm-suppress InternalMethod It's given by the api
 */
class HtmlReportGenerator implements AfterAnalysisInterface
{
    public const TEMPLATE_REPORT_FILE_PATH = 'report.html.twig';

    public static string $outputFilePath = 'report.html';

    public const SHOW_INFO_OPTION = 'show-info';

    /**
     * @param AfterAnalysisEvent $event
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     *
     * @return void
     */
    public static function afterAnalysis(AfterAnalysisEvent $event): void
    {
        echo "Generating Psalm Html Report in " . self::$outputFilePath . PHP_EOL;

        $options  = getopt('', [self::SHOW_INFO_OPTION]);
        $showInfo = false;

        if (array_key_exists(self::SHOW_INFO_OPTION, $options)) {
            $showInfo = true;
        }

        $codebase    = $event->getCodebase();
        $psalmIssues = $event->getIssues();
        $twig        = new Environment(
            new FilesystemLoader(__DIR__ . '/../src/templates', 'psalm-html-report')
        );

        $totalIssues = 0;
        $issues      = [];

        /** @var list<IssueData> $fileIssues */
        foreach ($psalmIssues as $filePath => $fileIssues) {
            foreach ($fileIssues as $issue) {
                $severity = $issue->severity;

                if ($showInfo === false && $severity === 'info') {
                    continue;
                }

                $issues[$filePath][] = new Issue(
                    $issue->message,
                    $severity,
                    $issue->type,
                    trim($issue->snippet),
                    $issue->line_from,
                    $issue->link
                );

                $totalIssues += 1;
            }
        }

        $htmlReport = $twig->render(
            self::TEMPLATE_REPORT_FILE_PATH,
            [
                'report' => new Report(
                    $codebase->config->base_dir,
                    $codebase->analyzer->getTypeInferenceSummary($codebase),
                    $issues,
                    $totalIssues
                ),
            ]
        );

        file_put_contents(self::$outputFilePath, $htmlReport);
    }
}
