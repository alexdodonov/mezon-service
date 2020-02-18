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
     * Constructor
     *
     * @param mixed $securityProvider
     *            Security provider
     */
    public function __construct(
        $securityProvider = \Mezon\Service\ServiceMockSecurityProvider::class)
    {
        parent::__construct();

        if (is_string($securityProvider)) {
            $this->securityProvider = new $securityProvider($this->getParamsFetcher());
        } else {
            $this->securityProvider = $securityProvider;
        }
    }

    /**
     * Method creates parameters fetcher
     *
     * @return \Mezon\Service\ServiceRequestParamsInterface paremeters fetcher
     */
    public function createFetcher(): \Mezon\Service\ServiceRequestParamsInterface
    {
        return new \Mezon\Service\ServiceConsoleTransport\ConsoleRequestParams($this->router);
    }

    /**
     * Method runs router
     */
    public function run(): void
    {
        $this->result = $this->router->callRoute($_GET['r']);
    }
}
