<?
namespace worker;

use PDOException;

include_once("passwordConfig.php");

class PasswordWrk {

    public function hash_password(string $password)
    {
        return crypt($password, SALT);
    }

    public function verify_password(string $password, string $hashedPassword)
    {
        return hash_equals($this->hash_password($password), $hashedPassword);
    }

    public function password_hash_get(string $username): string
    {
        try {
            $passwordHash = Connexion::getInstance()->selectQueryOne(
                "SELECT password FROM T_User WHERE username = :username",
                array("username" => $username)
            )["password"];
            return $passwordHash;
        } catch (PDOException $e) {
            http_response_code(404);
            echo "Can not get password. " . $e->getMessage();
            die();
        }
    }
}
