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
        if (!isset($_POST["code"])) {
            http_response_code(400);
            echo "Code in body is not set.";
            die();
        }
        $username = $_POST["username"];
        $code = $_POST["code"];

        $codeController = new CodeController();
        $codeController->verify_code($username, $code);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo "serveur error " . $e->getMessage();
    die();
}