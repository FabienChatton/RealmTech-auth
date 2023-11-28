<?
namespace controller;

use Exception;
use worker\Connexion;

class AuthController {

    public function __construct() {
        
    }

    public function register_new_user(string $username, string $password)
    {
        try {
            Connexion::getInstance()->executeQuery(
                "INSERT INTO T_User (username, password) VALUES (:username, :password)",
                array("username" => $username, "password" => password_hash($password, PASSWORD_DEFAULT))
            );
            http_response_code(200);
            echo "ok";
            die();
        } catch (Exception $e) {
            http_response_code(500);
            echo "The new user has an exception " . $e->getMessage();
            die();
        }
    }

    public function verify_password(string $username, string $password)
    {
        try {
            $hashPassword = Connexion::getInstance()->selectQueryOne(
                "SELECT password FROM T_User WHERE username = :username",
                array("username" => $username)
            )["password"];
            if (password_verify($password, $hashPassword)) {
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
}