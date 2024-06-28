<?
namespace controller;

use Exception;
use PDOException;
use worker\Connexion;
use worker\PasswordWrk;
use controller\EphemeralNameController;

class AccessTokenController {
    const ACCESS_TOKEN_DIFF_LIMITE = 60; // one minute
    private $passwordWrk;
    public function __construct() {
        $this->passwordWrk = new PasswordWrk();
    }

    public function create_access_token(string $username, string $password, ?string $ephemeralName)
    {
        try {
            $passwordHash = $this->passwordWrk->password_hash_get($username);
            if (!$this->passwordWrk->verify_password($password, $passwordHash)) {
                http_response_code(401);
                echo "Not authrised";
                die();
            }
            $clientSecret = $this->generate_client_secrete();
            $accessToken = $this->generate_access_token($clientSecret, $ephemeralName);
            Connexion::getInstance()->executeQuery(
                "UPDATE T_User SET access_token = :access_token WHERE username = :username",
                array("access_token" => $accessToken, "username" => $username)
            );
            if ($ephemeralName) {
                header("Content-Type: application/json");
                $res = [
                    "message" => "ok",
                    "secretClient" => $clientSecret,
                ];
                echo json_encode($res);
                die();
            } else {
                echo "ok";
                die();
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo $e->getMessage();
            die();
        }
    }

    public function verify_access_token(string $username)
    {
        try {
            $playerRecord = null;
            try {
                $playerRecord = Connexion::getInstance()->selectQueryOne(
                    "SELECT access_token, uuid FROM T_User WHERE username = :username",
                    array("username" => $username)
                );
            } catch (PDOException $e) {
                http_response_code(404);
                echo "Player $username don't existe";
                die();
            }
            $accessToken = $playerRecord["access_token"];
            $playerUuid = $playerRecord["uuid"];

            $this->invalidate_access_token($username);

            $accessTokenTimestemp = intval(explode(":", $accessToken)[0]);
            $timestep = time();
            if (!($timestep - $accessTokenTimestemp < self::ACCESS_TOKEN_DIFF_LIMITE)) {
                http_response_code(401);
                echo "Access token not valide";
                die();
            }
            echo $playerUuid;
            die();
        } catch (Exception $e) {
            http_response_code(500);
            echo $e->getMessage();
            die();
        }
    }

    public function verify_access_token_with_client_secret(string $username, string $clientSecret, string $ephemeralName)
    {
        try {
            $playerRecord = null;
            try {
                $playerRecord = Connexion::getInstance()->selectQueryOne(
                    "SELECT access_token, uuid FROM T_User WHERE username = :username",
                    array("username" => $username)
                );
            } catch (PDOException $e) {
                http_response_code(404);
                echo "Player $username don't existe";
                die();
            }
            $this->invalidate_access_token($username);

            $accessToken = $playerRecord["access_token"];
            $playerUuid = $playerRecord["uuid"];

            $tokenExplode = explode(":", $accessToken);

            $accessTokenTimestemp = intval($tokenExplode[0]);
            $clientSecretStorred = $tokenExplode[1];
            $ephemeralNameStorred = $tokenExplode[2];

            $timestep = time();
            if (!($timestep - $accessTokenTimestemp < self::ACCESS_TOKEN_DIFF_LIMITE)) {
                http_response_code(401);
                echo "Access token expirred";
                die();
            }

            if ($clientSecret != $clientSecretStorred) {
                http_response_code(401);
                echo "Access token client secret invalide";
                die();
            }

            if ($ephemeralName != $ephemeralNameStorred) {
                http_response_code(401);
                echo "Access token ephemeral name invalide";
                die();
            }
            header("Content-Type: application/json");
            $res = [
                "uuid" => $playerUuid,
                "protocoleLevel" => 2, // this is the second version
            ];
            echo json_encode($res);
            die();
        } catch (Exception $e) {
            http_response_code(500);
            echo $e->getMessage();
            die();
        }
    }

    private function generate_client_secrete()
    {
        return base64_encode(random_bytes(50));
    }

    private function generate_access_token(string $secrete, ?string $ephemeralName) 
    {
        if ($ephemeralName) {
            $ephemeralNameOrRamdom = $ephemeralName;
        } else {
            $ephemeralNameOrRamdom = base64_encode(random_bytes(49));
        }
        return time() . ":" . $secrete . ":" . $ephemeralNameOrRamdom;
    }

    private function invalidate_access_token(string $username)
    {
        Connexion::getInstance()->executeQuery(
            "UPDATE T_User SET access_token = :access_token WHERE username = :username",
            array("access_token" => "", "username" => $username)
        );
    }
}