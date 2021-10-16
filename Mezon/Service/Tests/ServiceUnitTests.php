<?php
namespace Mezon\Service\Tests;

use PHPUnit\Framework\TestCase;
use Mezon\Service\Service;
use Mezon\Service\ServiceLogic;
use Mezon\Service\ServiceModel;
use Mezon\Security\MockProvider;
use Mezon\Service\ServiceRestTransport\ServiceRestTransport;
use Mezon\Service\ServiceHttpTransport\ServiceHttpTransport;
use Mezon\Service\ServiceConsoleTransport\ServiceConsoleTransport;
use Mezon\Service\Tests\Mocks\TestingTransport;
use Mezon\Service\ServiceBaseLogic;
use Mezon\Transport\Tests\MockParamsFetcher;

/**
 * Class ServiceUnitTests
 *
 * @package Service
 * @subpackage ServiceUnitTests
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/17)
 * @copyright Copyright (c) 2019, aeon.org
 */
define('AS_STRING', 1);
define('AS_OBJECT', 2);

/**
 * Common service unit tests
 *
 * @author Dodonov A.A.
 * @psalm-suppress PropertyNotSetInConstructor
 */
class ServiceUnitTests extends TestCase
{

    /**
     * Testing method
     */
    public function testExceptionWhileConstruction(): void
    {
        // setup and test body
        ob_start();
        new ExceptionTestingService(new TestingTransport(new MockProvider()));
        $content = ob_get_contents();
        ob_end_clean();

        // assertions
        $this->assertStringContainsString("message", $content);
        $this->assertStringContainsString("code", $content);
        $this->assertTrue(is_array(json_decode($content, true)));
    }
}
