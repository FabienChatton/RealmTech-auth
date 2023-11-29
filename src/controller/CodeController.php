<?
namespace controller;

use worker\CodeWrk;

class CodeController {
    private $codeWrk;

    public function __construct() {
        $this->codeWrk = new CodeWrk();
    }

    public function verify_code($username, $code)
    {
        if ($this->codeWrk->verify_code($username, $code)) {
            echo "ok";
            die();
        } else {
            http_response_code(401);
            echo "code not valide";
            die();
        }
    }
}