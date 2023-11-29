<?

use controller\AuthController;

include_once("autoloader.php");

try {
    if (isset($_SERVER["REQUEST_METHOD"])) {
        if (!isset($_POST["password"])) {
            http_response_code(400);
            echo "Password in body is not set.";
            die();
        }
        $password = $_POST["password"];
        
        $authController = new AuthController();
        $authController->hash_password($password);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo "serveur error " . $e->getMessage();
    die();
}