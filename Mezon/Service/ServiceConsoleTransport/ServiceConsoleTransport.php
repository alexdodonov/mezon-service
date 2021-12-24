<?php
namespace Mezon\Service\ServiceConsoleTransport;

use Mezon\Service\Transport;
use Mezon\Transport\RequestParamsInterface;

/**
 * Class ServiceConsoleTransport
 *
 * @package Service
 * @subpackage ServiceConsoleTransport
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/07)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * Console transport for all services
 */
class ServiceConsoleTransport extends Transport
{

    /**
     * Execution result
     */
    public static $result;

    /**
     * Method creates parameters fetcher
     *
     * @return RequestParamsInterface paremeters fetcher
     */
    public function createFetcher(): RequestParamsInterface
    {
        return new ConsoleRequestParams($this->getRouter());
    }

    /**
     * Method runs router
     */
    public function run(): void
    {
        static::$result = $this->getRouter()->callRoute($_GET['r']);
    }

    /**
     * Method creates session
     *
     * @param bool|string $token
     *            Session token
     */
    public function createSession(string $token): string
    {
        return $token;
    }
}
