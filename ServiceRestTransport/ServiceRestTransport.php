<?php
namespace Mezon\Service\ServiceRestTransport;

/**
 * Class ServiceRestTransport
 *
 * @package Service
 * @subpackage ServiceRestTransport
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/17)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * REST transport for all services.
 *
 * @author Dodonov A.A.
 */
class ServiceRestTransport extends \Mezon\Service\ServiceHttpTransport\ServiceHttpTransport
{

    /**
     * Method runs logic functions.
     *
     * @param \Mezon\Service\ServiceBaseLogicInterface $serviceLogic
     *            -
     *            object with all service logic.
     * @param string $method
     *            -
     *            logic's method to be executed.
     * @param array $params
     *            -
     *            logic's parameters.
     * @return mixed Result of the called method.
     */
    public function callLogic(\Mezon\Service\ServiceBaseLogicInterface $serviceLogic, string $method, array $params = [])
    {
        $this->header('Content-type', 'application/json');

        try {
            $params['SessionId'] = $this->createSession();

            return call_user_func_array([
                $serviceLogic,
                $method
            ], $params);
        } catch (\Mezon\Service\ServiceRestTransport\RestException $e) {
            return $this->errorResponse($e);
        } catch (\Exception $e) {
            return $this->parentErrorResponse($e);
        }
    }

    /**
     * Method runs logic functions.
     *
     * @param \Mezon\Service\ServiceBaseLogicInterface $serviceLogic
     *            -
     *            object with all service logic.
     * @param string $method
     *            -
     *            logic's method to be executed.
     * @param array $params
     *            -
     *            logic's parameters.
     * @return mixed Result of the called method.
     */
    public function callPublicLogic(
        \Mezon\Service\ServiceBaseLogicInterface $serviceLogic,
        string $method,
        array $params = [])
    {
        $this->header('Content-type', 'application/json');

        try {
            return call_user_func_array([
                $serviceLogic,
                $method
            ], $params);
        } catch (\Mezon\Service\ServiceRestTransport\RestException $e) {
            return $this->errorResponse($e);
        } catch (\Exception $e) {
            return $this->parentErrorResponse($e);
        }
    }

    /**
     * Method runs router
     *
     * @codeCoverageIgnore
     */
    public function run(): void
    {
        print(json_encode($this->router->callRoute($_GET['r'])));
    }

    /**
     * Error response compilator
     *
     * @param mixed $e
     *            Exception object
     * @return array Error data
     */
    public function errorResponse($e): array
    {
        $return = [
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
            'service' => 'service',
            'call_stack' => $this->formatCallStack($e),
            'host' => 'console'
        ];

        if ($e instanceof \Mezon\Service\ServiceRestTransport\RestException) {
            $return['http_code'] = $e->getHttpCode();
            $return['http_body'] = $e->getHttpBody();
        }

        return $return;
    }

    /**
     * Error response compilator
     *
     * @param mixed $e
     *            Exception object
     * @return array Error data
     */
    public function parentErrorResponse($e): array
    {
        return parent::errorResponse($e);
    }

    /**
     * Method processes exception
     *
     * @param $e \Exception
     *            object
     * @codeCoverageIgnore
     */
    public function handleException($e): void
    {
        header('Content-type:application/json');

        print(json_encode($this->errorResponse($e)));
    }
}
