<?php
namespace Mezon\Service;

/**
 * Class ServiceMockSecurityProvider
 *
 * @package Service
 * @subpackage ServiceMockSecurityProvider
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/06)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * Class ServiceMockSecurityProvider - provides mockes for all security methods
 */
class ServiceMockSecurityProvider implements \Mezon\Service\ServiceSecurityProviderInterface
{

    /**
     * Method creates session from existing token or fetched from HTTP headers
     *
     * @param string $token
     *            Session token
     * @return string Session token
     */
    public function createSession(string $token = null): string
    {
        if ($token === null) {
            return md5(microtime(true));
        } else {
            return $token;
        }
    }

    /**
     * Method creates conection session
     *
     * @param string $login
     *            Login
     * @param string $password
     *            Password
     * @return string Random md5 hash as session id
     */
    public function connect(string $login, string $password): string
    {
        return md5(microtime(true));
    }

    /**
     * Method sets session token
     *
     * @param string $token
     *            Token
     * @return string Session token id
     */
    public function setToken(string $token): string
    {
        return $token;
    }

    /**
     * Method returns id of the session user
     *
     * @param string $token
     *            Token
     * @return int id of the session user
     */
    public function getSelfId(string $token): int
    {
        return 1;
    }

    /**
     * Method returns login of the session user
     *
     * @param string $token
     *            Token
     * @return string login of the session user
     */
    public function getSelfLogin(string $token): string
    {
        return 'admin@localhost';
    }

    /**
     * Method allows user to login under another user
     *
     * @param string $token
     *            Token
     * @param string $loginOrId
     *            In this field login or user id are passed
     * @param string $field
     *            Contains 'login' or 'id'
     * @return string New session id
     */
    public function loginAs(string $token, string $loginOrId, string $field): string
    {
        return $token;
    }

    /**
     * Method returns true or false if the session user has permit or not
     *
     * @param string $token
     *            Token
     * @param string $permit
     *            Permit name
     * @return bool True if the
     */
    public function hasPermit(string $token, string $permit): bool
    {
        return true;
    }

    /**
     * Method throws exception if the user does not have permit
     *
     * @param string $token
     *            Token
     * @param string $permit
     *            Permit name
     */
    public function validatePermit(string $token, string $permit)
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Mezon\Service\ServiceSecurityProviderInterface::getLoginFieldName()
     */
    public function getLoginFieldName(): string
    {
        return 'login';
    }

    /**
     * Method returns field name for session_id
     *
     * @return string Field name
     */
    public function getSessionIdFieldName(): string
    {
        return 'session_id';
    }
}
