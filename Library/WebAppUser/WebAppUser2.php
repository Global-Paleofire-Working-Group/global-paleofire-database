<?php

abstract class WebAppUser {

    /**
     * Login of the webApp User
     * */
    private $_wepAppUser_login;

    /**
     * Password of the webApp User
     * */
    protected $_wepAppUser_password; // RM WAS PRIVATE ON PROD SERVER BUT PROTECTED ON DEV

    /**
     * Constuct a WebAppUser
     * @param $login    login of the webApp user
     */
    public function __construct($login, $password) {
        $this->_wepAppUser_login = $login;
        $this->_wepAppUser_password = $password;
    }

    /**
     * @param $login_value login of the webAppUser
     */
    public function setWebAppUserLogin($login_value) {
        if ($login_value != NULL) {
            $this->_wepAppUser_login = $login_value;
        } else {
            throw new Exception("The login can't be NULL");
        }
    }

    /**
     * @param $password_value password of the webAppUser
     */
    public function setWebAppUserPassword($password_value) {
        if ($password_value != NULL) {
            $this->_wepAppUser_password = $password_value;
        } else {
            throw new Exception("The password can't be NULL");
        }
    }

    public function getWebAppUserLogin() {
        return $this->_wepAppUser_login;
    }

    public function getWebAppUserPassword() {

        return $this->_wepAppUser_password;
    }

    public static function encrypt_decrypt($action, $string) {
        $output = false;

        $encrypt_method = "AES-256-CBC";
        $secret_key = 'This is my secret key';
        $secret_iv = 'This is my secret iv';

        // hash
        $key = hash('sha256', $secret_key);

        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);

        if ($action == 'encrypt') {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        } else if ($action == 'decrypt') {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }

        return $output;
    }

    public function create() {
        if ($this->getWebAppUserLogin() != NULL && $this->getWebAppUserLogin() != "") {
            //check if login already exists
            if ($this->loginExistsInDataBase($this->getWebAppUserLogin())) {
                throw new Exception("create webAppUser : The login already exists");
            } else {
                //check password
                if ($this->getWebAppUserPassword() != NULL && $this->getWebAppUserPassword() != "") {
                    $id_role = $this->getWebAppUserRole()->getIdentifier();
                    if ($id_role != NULL && $id_role() != "") {
                        $this->createInDatabase($this->getWebAppUserLogin(), $this->getWebAppUserPassword(), $id_role);
                    } else {
                        throw new Exception("create webAppUser : The role can't be NULL or empty");
                    }
                } else {
                    throw new Exception("create webAppUser : The password can't be NULL or empty");
                }
            }
        } else {
            throw new Exception("create webAppUser : The login can't be NULL or empty");
        }
    }

    abstract protected function loginExistsInDataBase($login_value);

    abstract protected function createInDatabase($login_value, $encrypted_password, $id_role);

    abstract public function checkPassword();

    abstract protected function getDatabaseId();

    abstract public function getRoleNumber();

    abstract protected function getRoleUserId();
}
