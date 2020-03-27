<?php
namespace Mezon\Service;

/**
 * Class ServiceAuthenticationSecurityProvider
 *
 * @package Service
 * @subpackage ServiceAuthenticationSecurityProvider
 * @author Dodonov A.A.
 * @version v.1.0 (2020/03/19)
 * @copyright Copyright (c) 2020, aeon.org
 */

/**
 * Class provides simple and the most common functionality
 */
class ServiceAuthenticationSecurityProvider implements \Mezon\Service\ServiceAuthenticationSecurityProviderInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Mezon\Service\ServiceAuthenticationSecurityProviderInterface::getSelfLogin()
     */
    public function getSelfLogin(): string
    {
        return $_SESSION['session-user-login'];
    }

    /**
     *
     * {@inheritdoc}
     * @see \Mezon\Service\ServiceAuthenticationSecurityProviderInterface::getLoginFieldName()
     */
    public function getLoginFieldName(): string
    {
        return 'login';
    }

    /**
     *
     * {@inheritdoc}
     * @see \Mezon\Service\ServiceAuthenticationSecurityProviderInterface::getSessionIdFieldName()
     */
    public function getSessionIdFieldName(): string
    {
        return 'session-id';
    }

    /**
     *
     * {@inheritdoc}
     * @see \Mezon\Service\ServiceAuthenticationSecurityProviderInterface::getSelfId()
     */
    public function getSelfId(): int
    {
        return $_SESSION['session-user-id'];
    }

    /**
     * Method creates session
     *
     * @param string $token
     * @codeCoverageIgnore
     */
    protected function sessionId(string $token): void
    {
        session_id($token);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Mezon\Service\ServiceAuthenticationSecurityProviderInterface::createSession()
     */
    public function createSession(string $token): string
    {
        $this->sessionId($token);

        if (isset($_SESSION['session-user-login']) === false) {
            throw (new \Exception('Authentication error', - 1));
        }

        return $token;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Mezon\Service\ServiceAuthenticationSecurityProviderInterface::connect()
     */
    public function connect(string $login, string $password): string
    {
        $token = md5(microtime(true));

        $this->sessionId($token);

        $_SESSION['session-user-login'] = $login;
        $_SESSION['session-user-id'] = 1;

        return $token;
    }
}
