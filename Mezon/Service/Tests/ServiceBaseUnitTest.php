<?php
namespace Mezon\Service\Tests;

use PHPUnit\Framework\TestCase;
use Mezon\Service\ServiceBase;
use Mezon\Service\ServiceHttpTransport\ServiceHttpTransport;
use Mezon\Service\ServiceRestTransport\ServiceRestTransport;
use Mezon\Service\ServiceConsoleTransport\ServiceConsoleTransport;
use Mezon\Conf\Conf;
use Mezon\Service\Tests\Mocks\ExceptionTestingBaseService;

/**
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
class ServiceBaseUnitTest extends TestCase
{

    /**
     *
     * {@inheritdoc}
     * @see TestCase::setUp()
     */
    protected function setUp(): void
    {
        Conf::setConfigStringValue('system/layer', 'mock');
    }

    // TODO split this file into parts

    /**
     * Testing getTransport method
     */
    public function testGetTransport(): void
    {
        // setup
        $service = new ServiceBase(new ServiceHttpTransport());

        // test body and assertions
        $this->assertInstanceOf(ServiceHttpTransport::class, $service->getTransport());
    }

    /**
     * Testing getTransport method
     */
    public function testSetTransport(): void
    {
        // setup
        $service = new ServiceBase(new ServiceHttpTransport());

        // assertions
        $this->assertInstanceOf(ServiceHttpTransport::class, $service->getTransport());

        // test body
        $service->setTransport(new ServiceRestTransport());

        // assertions
        $this->assertInstanceOf(ServiceRestTransport::class, $service->getTransport());
    }

    /**
     * Testing exception handling while constructor call
     */
    public function testExceptionWhileConstruction(): void
    {
        // setup and assertions
        ob_start();
        new ExceptionTestingBaseService(new ServiceConsoleTransport());
        $content = ob_get_contents();
        ob_end_clean();

        // assertions
        $this->assertStringContainsString('"message"', $content);
        $this->assertStringContainsString('"code"', $content);
        $this->assertTrue(is_array(json_decode($content, true)));
    }
}
