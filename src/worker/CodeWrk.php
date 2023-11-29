<?
namespace worker;

class CodeWrk {
    private $passwordWrk;

    public function __construct() {
        $this->passwordWrk = new PasswordWrk();
    }

    public function generate_code(string $username, string $passwordHash)
    {
        return hash('sha256', $username . $passwordHash);
    }

    public function verify_code(string $username, string $codeFromClient)
    {
        $passwordHash = $this->passwordWrk->password_hash_get($username);
        $code = $this->generate_code($username, $passwordHash);
        return $this->compare_code($code, $codeFromClient);
    }

    private function compare_code(string $codeFromServer, string $codeFromClient)
    {
        return hash_equals($codeFromServer, $codeFromClient);
    }
}