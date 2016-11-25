<?php

namespace Usuarios\Model;

use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as AuthAdapter;
/**
 * Description of Login
 *
 * @author Andres
 */
class Base {

    private $auth;
    private $authAdapter;

    const NOT_IDENTITY = 'notIdentity';
    const INVALID_CREDENTIAL = 'invalidCredential';
    const INVALID_USER = 'invalidUser';
    const INVALID_LOGIN = 'invalidLogin';

    /**
     * Mensaje de validaciones por defecto
     *
     * @var array
     */
    protected $messages = array(
        self::NOT_IDENTITY => "Not existent identity. A record with the supplied identity could not be found.",
        self::INVALID_CREDENTIAL => "Invalid credential. Supplied credential is invalid.",
        self::INVALID_USER => "Invalid User. Supplied credential is invalid",
        self::INVALID_LOGIN => "Invalid Login. Fields are empty"
    );

    public function __construct($dbAdapter) {
        $this->authAdapter = new AuthAdapter($dbAdapter);
        $this->authAdapter
                ->setTableName("usuarios")
                ->setIdentityColumn("email")
                ->setCredentialColumn("md5");
        $this->auth = new AuthenticationService();
    }

    public function getIdentity() {
        if ($this->auth->hasIdentity()) {
            return $this->auth->getIdentity();
        }
        return null;
    }

    /**
     * @param string $messageString
     * @param string $messageKey    OPTIONAL
     * @return UserModel
     * @throws Exception
     */
    public function setMessage($messageString, $messageKey = null) {
        if ($messageKey === null) {
            $keys = array_keys($this->messages);
            $messageKey = current($keys);
        }
        if (!isset($this->messages[$messageKey])) {
            throw new \Exception("No message exists for key '$messageKey'");
        }
        $this->messages[$messageKey] = $messageString;
        return $this;
    }

    /**
     * @param array $messages
     * @return UserModel
     */
    public function setMessages(array $messages) {
        foreach ($messages as $key => $message) {
            $this->setMessage($message, $key);
        }
        return $this;
    }

}