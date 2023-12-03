<?
namespace controller;

use Exception;
use worker\Connexion;
use worker\PasswordWrk;

class AccessTokenController {
    const ACCESS_TOKEN_DIFF_LIMITE = 60; // one minute
    private $passwordWrk;
    public function __construct() {
        $this->passwordWrk = new PasswordWrk();
    }

    public function create_access_token(string $username, string $password)
    {
        try {
            $passwordHash = $this->passwordWrk->password_hash_get($username);
            if (!$this->passwordWrk->verify_password($password, $passwordHash)) {
                http_response_code(401);
                echo "Not authrised";
                die();
            }
            $accessToken = $this->generate_access_token();
            Connexion::getInstance()->executeQuery(
                "UPDATE T_User SET access_token = :access_token WHERE username = :username",
                array("access_token" => $accessToken, "username" => $username)
            );
            echo "ok";
            die();
        } catch (Exception $e) {
            http_response_code(500);
            echo $e->getMessage();
            die();
        }
    }

    public function verify_access_token(string $username)
    {
        try {
            // get access token
            $accessToken = Connexion::getInstance()->selectQueryOne(
                "SELECT access_token FROM T_User WHERE username = :username",
                array("username" => $username)
            )["access_token"];

            $this->invalidate_access_token($username);

            $accessTokenTimestemp = intval(explode(":", $accessToken)[0]);
            $timestep = time();
            if (!($timestep - $accessTokenTimestemp < self::ACCESS_TOKEN_DIFF_LIMITE)) {
                http_response_code(401);
                echo "Access token not valide";
                die();
            }
            echo "access granted";
            die();
        } catch (Exception $e) {
            http_response_code(500);
            echo $e->getMessage();
            die();
        }
    }

    private function generate_access_token() 
    {
        return time() . ":" . base64_encode(random_bytes(100));
    }

    private function invalidate_access_token(string $username)
    {
        Connexion::getInstance()->executeQuery(
            "UPDATE T_User SET access_token = :access_token WHERE username = :username",
            array("access_token" => "", "username" => $username)
        );
    }
}