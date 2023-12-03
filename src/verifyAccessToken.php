<?

use controller\AccessTokenController;

include_once("autoloader.php");

try {
    if (isset($_SERVER["REQUEST_METHOD"])) {
        if (!isset($_POST["username"])) {
            http_response_code(400);
            echo "Username in body is not set.";
            die();
        }
        $username = $_POST["username"];

        $accessTokenController = new AccessTokenController();
        $accessTokenController->verify_access_token($username);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo "serveur error " . $e->getMessage();
    die();
}