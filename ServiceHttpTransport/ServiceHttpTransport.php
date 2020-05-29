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
class ServiceHttpTransport extends \Mezon\Service\Transport
{

    /**
     * Method creates session from existing token or fetched from HTTP headers
     *
     * @param string $token
     *            Session token
     * @return string Session token
     */
    public function createSession(string $token): string
    {
        return $this->getSecurityProvider()->createSession($token);
    }

    /**
     * Method creates parameters fetcher
     *
     * @return \Mezon\Transport\RequestParamsInterface paremeters fetcher
     */
    public function createFetcher(): \Mezon\Transport\RequestParamsInterface
    {
        return new \Mezon\Transport\HttpRequestParams($this->getRouter());
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
        @header($header . ':' . $value);
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
        $this->header('Content-Type', 'text/html; charset=utf-8');

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
        $this->header('Content-Type', 'text/html; charset=utf-8');

        return parent::callPublicLogic($serviceLogic, $method, $params);
    }

    /**
     * Method outputs exception data
     *
     * @param array $e
     *            exception data
     */
    public function outputException(array $e): void
    {
        $this->header('Content-Type', 'text/html; charset=utf-8');

        print(json_encode($e));
    }
}
