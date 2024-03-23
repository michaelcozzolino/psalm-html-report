<?php declare(strict_types=1);

namespace MichaelCozzolino\PsalmHtmlReport;

use Psalm\Plugin\PluginEntryPointInterface;
use Psalm\Plugin\RegistrationInterface;
use SimpleXMLElement;
use function class_exists;

class Plugin implements PluginEntryPointInterface
{
    public function __invoke(RegistrationInterface $registration, ?SimpleXMLElement $config = null): void
    {
        class_exists(HtmlReportGenerator::class, true);
        $registration->registerHooksFromClass(HtmlReportGenerator::class);
    }
}
