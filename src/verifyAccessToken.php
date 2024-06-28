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
        $secretClient = $_POST["secretClient"];
        $ephemeralName = $_POST["ephemeralName"];

        $accessTokenController = new AccessTokenController();
        if ($secretClient) {
            $accessTokenController->verify_access_token_with_client_secret($username, $secretClient, $ephemeralName);
        } else {
            $accessTokenController->verify_access_token($username);
        }
    }
} catch (Exception $e) {
    http_response_code(500);
    echo "serveur error " . $e->getMessage();
    die();
}