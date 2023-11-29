<?
namespace worker;

use worker\Connexion;

class CodeWrk {
    public function generate_code(string $username, string $passwordHash)
    {
        return hash('sha256', $username . $passwordHash);
    }

    public function verify_code(string $username, $codeFromClient)
    {
        $passwordHash = Connexion::getInstance()->selectQueryOne(
            "SELECT password FROM T_User WHERE username = :username",
            array("username" => $username)
        )["password"];

        $code = $this->generate_code($username, $passwordHash);

        return $this->compare_code($code, $codeFromClient);
    }

    private function compare_code(string $codeFromServer, string $codeFromClient)
    {
        return hash_equals($codeFromServer, $codeFromClient);
    }
}