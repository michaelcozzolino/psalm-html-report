<?php declare(strict_types=1);

namespace MichaelCozzolino\PsalmHtmlReport;

use Psalm\Plugin\PluginEntryPointInterface;
use Psalm\Plugin\RegistrationInterface;
use SimpleXMLElement;
use function array_key_exists;
use function class_exists;
use function getopt;
use function property_exists;

class Plugin implements PluginEntryPointInterface
{
    public const REPORT_OPTION = 'report';

    public function __invoke(RegistrationInterface $registration, ?SimpleXMLElement $config = null): void
    {
        $options = getopt('', [self::REPORT_OPTION . ':']);

        if (array_key_exists(self::REPORT_OPTION, $options)) {
            return;
        }

        if ($config !== null) {
            if (property_exists($config, 'outputFilePath')) {
                HtmlReportGenerator::$outputFilePath = (string) $config->outputFilePath;
            }
        }

        class_exists(HtmlReportGenerator::class, true);
        $registration->registerHooksFromClass(HtmlReportGenerator::class);
    }
}
