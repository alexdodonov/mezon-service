<?php
namespace Mezon\Service\ServiceConsoleTransport;

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
class ServiceConsoleTransport extends \Mezon\Service\ServiceTransport
{

    /**
     * Execution result
     */
    public $result;

    /**
     * Method creates parameters fetcher
     *
     * @return \Mezon\Service\ServiceRequestParamsInterface paremeters fetcher
     */
    public function createFetcher(): \Mezon\Service\ServiceRequestParamsInterface
    {
        return new \Mezon\Service\ServiceConsoleTransport\ConsoleRequestParams($this->getRouter());
    }

    /**
     * Method runs router
     */
    public function run(): void
    {
        $this->result = $this->getRouter()->callRoute($_GET['r']);
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
