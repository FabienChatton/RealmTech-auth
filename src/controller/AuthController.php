<?
namespace controller;

use Exception;
use worker\Connexion;
use worker\PasswordWrk;

class AuthController {
    private $passwordWrk;

    public function __construct() {
        $this->passwordWrk = new PasswordWrk();
    }

    public function register_new_user(string $username, string $password)
    {
        try {
            Connexion::getInstance()->executeQuery(
                "INSERT INTO T_User (username, password) VALUES (:username, :password)",
                array("username" => $username, "password" => $this->passwordWrk->hash_password($password))
            );
            http_response_code(200);
            echo "ok";
            die();
        } catch (Exception $e) {
            if (!strpos($e->getMessage(), "SQLSTATE[23000]: Integrity constraint violation: 1062")) {
                http_response_code(400);
                echo "A user with this username aleredy existe.";
                die();
            }
            http_response_code(500);
            echo $e->getMessage();
            die();
        }
    }

    public function verify_password(string $username, string $password)
    {
        try {
            $hashPassword = $this->passwordWrk->password_hash_get($username);

            if ($this->passwordWrk->verify_password($password, $hashPassword)) {
                echo "ok";
                die();
            } else {
                http_response_code(401);
                echo "password is not correct";
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo "The password can not be verified " . $e->getMessage();
            die();
        }
    }

    public function hash_password(string $password)
    {
        $hashPassword = $this->passwordWrk->hash_password($password);
        echo $hashPassword;
    }
}