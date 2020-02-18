<?php
namespace Mezon\Service;

/**
 * Class ServiceSecurityProviderInterface
 *
 * @package Service
 * @subpackage ServiceSecurityProviderInterface
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/08)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * Interface for security providers
 */
interface ServiceSecurityProviderInterface
{

    /**
     * Method creates session from existing token or fetched from HTTP headers
     *
     * @param string $token
     *            Session token
     * @return string Session token
     */
    public function createSession(string $token = ''): string;

    /**
     * Method creates conection session
     *
     * @param string $login
     *            Login
     * @param string $password
     *            Password
     * @return string Session id of the created session
     */
    public function connect(string $login, string $password): string;

    /**
     * Method sets session token
     *
     * @param string $token
     *            Token
     * @return string Session token id
     */
    public function setToken(string $token): string;

    /**
     * Method returns id of the session user
     *
     * @param string $token
     *            Token
     * @return int id of the session user
     */
    public function getSelfId(string $token): int;

    /**
     * Method returns login of the session user
     *
     * @param string $token
     *            Token
     * @return string login of the session user
     */
    public function getSelfLogin(string $token): string;

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
    public function loginAs(string $token, string $loginOrId, string $field): string;

    /**
     * Method returns true or false if the session user has permit or not
     *
     * @param string $token
     *            Token
     * @param string $permit
     *            Permit name
     * @return bool True if the user has permit
     */
    public function hasPermit(string $token, string $permit): bool;

    /**
     * Method throws exception if the user does not have permit
     *
     * @param string $token
     *            Token
     * @param string $permit
     *            Permit name
     */
    public function validatePermit(string $token, string $permit);

    /**
     * Method returns field name for login
     *
     * @return string Field name
     */
    public function getLoginFieldName(): string;

    /**
     * Method returns field name for session_id
     *
     * @return string Field name
     */
    public function getSessionIdFieldName(): string;
}
