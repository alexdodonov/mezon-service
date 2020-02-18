<?php
namespace Mezon\Service;

/**
 * Class ServiceClient
 *
 * @package Service
 * @subpackage ServiceClient
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/06)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * Service client for Service
 */
class ServiceClient extends \Mezon\CustomClient\CustomClient
{

    /**
     * Service name
     *
     * @var string
     */
    protected $service = '';

    /**
     * Last logged in user
     * This is used for performance improvements in ServiceClient::loginAs method
     * For optimisation purposes only! Do not use in the client code
     *
     * @var string
     */
    private $login = false;

    /**
     * Rewrite mode.
     * If true, then URLs like this /part1/part2/param1/ will be used. If false, then parameter ?r=part1/part2/param1 will be passed
     *
     * @var string
     */
    protected $rewriteMode = true;

    /**
     * Session id
     *
     * @var string
     */
    protected $sessionId = false;

    /**
     * Constructor
     *
     * @param string $service
     *            Service URL or service name
     * @param string $login
     *            Login
     * @param string $password
     *            Password
     * @param array $headers
     *            Headers
     */
    public function __construct(string $service, string $login = '', string $password = '', array $headers = [])
    {
        if (\Mezon\DnsClient\DnsClient::serviceExists($service)) {
            $this->service = $service;
            parent::__construct(\Mezon\DnsClient\DnsClient::resolveHost($service), $headers);
        } elseif (strpos($service, 'http://') === false && strpos($service, 'https://') === false) {
            throw (new \Exception('Service ' . $service . ' was not found in DNS'));
        } else {
            parent::__construct($service, $headers);
        }

        if ($login !== '') {
            $this->connect($login, $password);
        }
    }

    /**
     * Method sends POST request to server
     *
     * @param string $endpoint
     *            Calling endpoint
     * @param array $data
     *            Request data
     * @return mixed Result of the request
     */
    public function sendPostRequest(string $endpoint, array $data = [])
    {
        $result = parent::sendPostRequest($endpoint, $data);

        return json_decode($result);
    }

    /**
     * Method sends GET request to server
     *
     * @param string $endpoint
     *            Calling endpoint
     * @return mixed Result of the remote call
     */
    public function sendGetRequest(string $endpoint)
    {
        $result = parent::sendGetRequest($endpoint);

        return json_decode($result);
    }

    /**
     * Method sends PUT request to server
     *
     * @param string $endpoint
     *            Calling endpoint
     * @param array $data
     *            Request data
     * @return mixed Result of the request
     */
    public function sendPutRequest(string $endpoint, array $data = [])
    {
        $result = parent::sendPutRequest($endpoint, $data);

        return json_decode($result);
    }

    /**
     * Method sends DELETE request to server
     *
     * @param string $endpoint
     *            Calling endpoint
     * @param array $data
     *            Request data
     * @return mixed Result of the remote call
     */
    public function sendDeleteRequest(string $endpoint, array $data = [])
    {
        $result = parent::sendDeleteRequest($endpoint, $data);

        return json_decode($result);
    }

    /**
     * Method validates result
     *
     * @param object $resultResult
     *            of the authorisation request
     */
    protected function validateSessionId(object $result)
    {
        if (isset($result->session_id) === false) {
            throw (new \Exception($result->message ?? 'Undefined message', $result->code ?? - 1));
        }
    }

    /**
     * Method connects to the REST server via login and password pair
     *
     * @param string $login
     *            Login
     * @param string $password
     *            Password
     */
    public function connect(string $login, string $password)
    {
        // authorization
        $data = [
            'login' => $login,
            'password' => $password
        ];

        $result = $this->sendPostRequest($this->getRequestUrl('connect'), $data);

        $this->validateSessionId($result);

        $this->login = $login;
        $this->sessionId = $result->session_id;
    }

    /**
     * Method sets token
     *
     * @param string $token
     *            Access token
     * @param string $login
     *            User login
     */
    public function setToken(string $token, string $login = '')
    {
        if ($token === '') {
            throw (new \Exception('Token not set', - 4));
        }

        $this->login = $login;
        $this->sessionId = $token;
    }

    /**
     * Method returns token
     *
     * @return string Session id
     */
    public function getToken(): string
    {
        return $this->sessionId;
    }

    /**
     * Method returns self id of the session
     *
     * @return string Session user's id
     */
    public function getSelfId(): string
    {
        $result = $this->sendGetRequest($this->getRequestUrl('selfId'));

        return isset($result->id) ? $result->id : $result;
    }

    /**
     * Method returns self login of the session
     *
     * @return string Session user's login
     */
    public function getSelfLogin(): string
    {
        $result = $this->sendGetRequest($this->getRequestUrl('selfLogin'));

        return isset($result->login) ? $result->login : $result;
    }

    /**
     * Method logins under another user
     * $field must be 'id' or 'login'
     *
     * @param string $user
     *            User credentials
     * @param string $field
     *            Field name for credentials
     */
    public function loginAs(string $user, string $field = 'id')
    {
        if ($field != 'id' && $this->login !== $user) {
            $result = $this->sendPostRequest($this->getRequestUrl('loginAs'), [
                $field => $user
            ]);

            $this->validateSessionId($result);

            $this->sessionId = $result->session_id;
        }

        if ($field == 'id') {
            $this->login = false;
        } else {
            $this->login = $user;
        }
    }

    /**
     * Method returns stored login
     *
     * @return string Stored login
     */
    public function getStoredLogin()
    {
        return $this->login;
    }

    /**
     * Method returns common headers
     *
     * @return array Headers
     */
    protected function getCommonHeaders(): array
    {
        $result = parent::getCommonHeaders();

        if ($this->sessionId !== false) {
            $result[] = "Cgi-Authorization: Basic " . $this->sessionId;
        }

        return $result;
    }

    /**
     * Method returns service
     *
     * @return string service
     */
    public function getService(): string
    {
        return $this->service;
    }

    /**
     * Setting rewrite mode for URLs
     *
     * @param bool $rewriteMode
     *            rewrite mode
     */
    public function setReqriteMode(bool $rewriteMode): void
    {
        $this->rewriteMode = $rewriteMode;
    }

    /**
     * Method returns concrete url byit's locator
     *
     * @param string $urlLocator
     *            url locator
     * @return string concrete URL
     */
    protected function getRequestUrl(string $urlLocator): string
    {
        $urlMap = [
            'loginAs' => $this->rewriteMode ? '/login-as/' : '?r=login-as',
            'selfLogin' => $this->rewriteMode ? '/self/login/' : '?r='.urlecode('self/login'),
            'selfId' => $this->rewriteMode ? '/self/id/' : '?r='.urlecode('self/id'),
            'connect' => $this->rewriteMode ? '/connect/' : '?r=connect'
        ];

        if (isset($urlMap[$urlLocator]) === false) {
            throw (new \Exception('Locator ' . $urlLocator . ' was not found'));
        }

        return $urlMap[$urlLocator];
    }
}
