<?php declare(strict_types=1);

namespace MichaelCozzolino\PsalmHtmlReport\Tests;

use MichaelCozzolino\PsalmHtmlReport\HtmlReportGenerator;
use MichaelCozzolino\PsalmHtmlReport\Plugin;
use PHPUnit\Framework\TestCase;
use Psalm\Plugin\RegistrationInterface;
use SimpleXMLElement;

/**
 * @covers \MichaelCozzolino\PsalmHtmlReport\Plugin
 */
class PluginTest extends TestCase
{
    public function testInvokeWithoutConfiguration(): void
    {
        $registrationMock = self::createMock(RegistrationInterface::class);

        $registrationMock->expects(self::once())
                         ->method('registerHooksFromClass')
                         ->with(HtmlReportGenerator::class);

        (new Plugin)($registrationMock);

        self::assertSame('report.html', HtmlReportGenerator::$outputFilePath);
    }

    public function testInvokeWithConfiguration(): void
    {
        $registrationMock = self::createMock(RegistrationInterface::class);

        $registrationMock->expects(self::once())->method('registerHooksFromClass');

        $outputFilePath = './custom-dir/html-report.html';

        $xml = <<<XML
                    <test xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
                        <outputFilePath>$outputFilePath</outputFilePath>
                    </test>
                XML;

        (new Plugin)($registrationMock, new SimpleXMLElement($xml));

        self::assertSame($outputFilePath, HtmlReportGenerator::$outputFilePath);
    }
}
