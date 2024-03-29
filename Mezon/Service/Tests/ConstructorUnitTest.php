<?php
namespace Mezon\Service\Tests;

use PHPUnit\Framework\TestCase;
use Mezon\Service\ServiceConsoleTransport\ServiceConsoleTransport;
use Mezon\Conf\Conf;
use Mezon\Service\Tests\Mocks\ExceptionTestingService;

/**
 * Class ConstructorUnitTest
 *
 * @package Service
 * @subpackage ConstructorUnitTest
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/17)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * Common service unit tests
 *
 * @author Dodonov A.A.
 * @psalm-suppress PropertyNotSetInConstructor
 */
class ConstructorUnitTest extends TestCase
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

    /**
     * Testing method
     */
    public function testExceptionWhileConstruction(): void
    {
        // setup and test body
        ob_start();
        new ExceptionTestingService(new ServiceConsoleTransport());
        $content = ob_get_contents();
        ob_end_clean();

        // assertions
        $this->assertStringContainsString("message", $content);
        $this->assertStringContainsString("code", $content);
        $this->assertTrue(is_array(json_decode($content, true)));
    }
}
