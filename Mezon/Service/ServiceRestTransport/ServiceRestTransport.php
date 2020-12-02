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
        $this->header('Content-Type', 'application/json');

        try {
            $params['SessionId'] = $this->createSession($this->getParamsFetcher()
                ->getParam('session_id'));

            return call_user_func_array([
                $serviceLogic,
                $method
            ], $params);
        } catch (\Mezon\Rest\Exception $e) {
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
        $this->header('Content-Type', 'application/json');

        try {
            return call_user_func_array([
                $serviceLogic,
                $method
            ], $params);
        } catch (\Mezon\Rest\Exception $e) {
            return $this->errorResponse($e);
        } catch (\Exception $e) {
            return $this->parentErrorResponse($e);
        }
    }

    /**
     * Method calls route in transport specific way
     */
    protected function callRoute(): void
    {
        print(json_encode($this->getRouter()->callRoute($_GET['r'])));
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
            'service' => $_SERVER['HTTP_HOST'] ?? 'unknown'
        ];

        if ($this->isDebug()) {
            $return['call_stack'] = $this->formatCallStack($e);
        }

        if ($e instanceof \Mezon\Rest\Exception) {
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
     * Method outputs exception data
     *
     * @param array $e
     *            exception data
     */
    public function outputException(array $e): void
    {
        $this->header('Content-Type', 'application/json');

        print(json_encode($e));
    }
}
