<?php
namespace Mezon\Service;

/**
 * Interface ServiceAuthorizationSecurityProviderInterface
 *
 * @package Service
 * @subpackage ServiceAuthorizationSecurityProviderInterface
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/08)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * Interface for security provider with authorization
 */
interface ServiceAuthorizationSecurityProviderInterface extends ServiceAuthenticationSecurityProviderInterface
{
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
}
