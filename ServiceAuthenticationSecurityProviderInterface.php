<?php
namespace Mezon\Service;

/**
 * Interface ServiceAuthenticationSecurityProviderInterface
 *
 * @package Service
 * @subpackage ServiceAuthenticationSecurityProviderInterface
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/08)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * Interface for security provider with authorization
 */
interface ServiceAuthenticationSecurityProviderInterface extends ServiceSecurityProviderInterface
{

    /**
     * Method creates session from existing token or fetched from HTTP headers
     *
     * @param string $token
     *            Session token
     * @return string Session token
     */
    public function createSession(string $token): string;

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
     * Method returns id of the session user
     *
     * @return int id of the session user
     */
    public function getSelfId(): int;

    /**
     * Method returns login of the session user
     *
     * @return string login of the session user
     */
    public function getSelfLogin(): string;

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
