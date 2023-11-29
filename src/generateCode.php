<?
use controller\CodeController;

include_once("autoloader.php");

try {
    if (isset($_SERVER["REQUEST_METHOD"])) {
        if (!isset($_POST["username"])) {
            http_response_code(400);
            echo "Username in body is not set.";
            die();
        }
        
        if (!isset($_POST["passwordHash"])) {
            http_response_code(400);
            echo "PasswordHash in body is not set.";
            die();
        }
        $username = $_POST["username"];
        $passwordHash = $_POST["passwordHash"];
        
        $codeController = new CodeController();
        $codeController->generate_code($username, $passwordHash);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo "serveur error " . $e->getMessage();
    die();
}