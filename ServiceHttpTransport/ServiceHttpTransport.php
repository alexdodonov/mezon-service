<?php
namespace Mezon\Service\ServiceHttpTransport;

/**
 * Class ServiceHttpTransport
 *
 * @package Service
 * @subpackage ServiceHttpTransport
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/13)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * HTTP transport for all services
 *
 * @author Dodonov A.A.
 */
class ServiceHttpTransport extends \Mezon\Service\ServiceTransport
{

    /**
     * Constructor
     *
     * @param mixed $securityProvider
     *            Security provider
     */
    public function __construct($securityProvider = \Mezon\Service\ServiceMockSecurityProvider::class)
    {
        parent::__construct();

        if (is_string($securityProvider)) {
            $this->securityProvider = new $securityProvider($this->getParamsFetcher());
        } else {
            $this->securityProvider = $securityProvider;
        }
    }

    /**
     * Method creates session from existing token or fetched from HTTP headers
     *
     * @param string $token
     *            Session token
     * @return string Session token
     */
    public function createSession(string $token = ''): string
    {
        return $this->securityProvider->createSession($token);
    }

    /**
     * Method creates parameters fetcher
     *
     * @return \Mezon\Service\ServiceRequestParamsInterface paremeters fetcher
     */
    public function createFetcher(): \Mezon\Service\ServiceRequestParamsInterface
    {
        return new \Mezon\Service\ServiceHttpTransport\HttpRequestParams($this->router);
    }

    /**
     * Method outputs HTTP header
     *
     * @param string $header
     *            Header name
     * @param string $value
     *            Header value
     * @codeCoverageIgnore
     */
    protected function header(string $header, string $value)
    {
        header($header . ':' . $value);
    }

    /**
     * Method runs logic functions
     *
     * @param \Mezon\Service\ServiceBaseLogicInterface $serviceLogic
     *            object with all service logic
     * @param string $method
     *            logic's method to be executed
     * @param array $params
     *            logic's parameters
     * @return mixed Result of the called method
     */
    public function callLogic(\Mezon\Service\ServiceBaseLogicInterface $serviceLogic, string $method, array $params = [])
    {
        $this->header('Content-type', 'text/html; charset=utf-8');

        return parent::callLogic($serviceLogic, $method, $params);
    }

    /**
     * Method runs logic functions
     *
     * @param \Mezon\Service\ServiceBaseLogicInterface $serviceLogic
     *            object with all service logic
     * @param string $method
     *            logic's method to be executed
     * @param array $params
     *            logic's parameters
     * @return mixed Result of the called method
     */
    public function callPublicLogic(
        \Mezon\Service\ServiceBaseLogicInterface $serviceLogic,
        string $method,
        array $params = [])
    {
        $this->header('Content-type', 'text/html; charset=utf-8');

        return parent::callPublicLogic($serviceLogic, $method, $params);
    }
}
