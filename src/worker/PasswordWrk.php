<?
namespace worker;

include_once("hashConfig.php");

class PasswordWrk {

    public function hash_password(string $password)
    {
        return crypt($password, SALT);
    }

    public function verify_password(string $password, string $hashedPassword)
    {
        return hash_equals($this->hash_password($password), $hashedPassword);
    }
}
