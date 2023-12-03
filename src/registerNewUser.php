<?

use controller\AuthController;
use helper\EmailRegex;
use worker\PasswordWrk;

include_once("autoloader.php");

try {
    if (isset($_SERVER["REQUEST_METHOD"])) {
        if (!isset($_POST["username"])) {
            http_response_code(400);
            echo "Username in body is not set.";
            die();
        }
        if (!isset($_POST["password"])) {
            http_response_code(400);
            echo "Password in body is not set.";
            die();
        }
        $username = $_POST["username"];
        $password = $_POST["password"];
        $email = $_POST["email"];
        if (strlen($username) < 5) {
            http_response_code(400);
            echo "Username must have a length bigger or equals then 5.";
            die();
        }
        if (strlen($username) > 15) {
            http_response_code(400);
            echo "Username must have a length smaller or equals then 15.";
            die();
        }
        if (!preg_match('/^[a-zA-z0-9]{5,15}$/', $username)) {
            http_response_code(400);
            echo "Username is not valide. Must contain only letter and be bigger or equals as 5 and smaller or equals as 15.";
            die();
        }
        if ($email != null) {
            $emailRegex = new EmailRegex();
            if (!$emailRegex->validate_email($email)) {
                http_response_code(400);
                echo "Email not valide";
                die();
            }
        }
        $passwordWrk = new PasswordWrk();
        $passwordHash = $passwordWrk->hash_password($password);
        if (!is_string($passwordHash)) {
            http_response_code(400);
            echo "Password is not valide";
            die();
        }
        $authController = new AuthController();
        $authController->register_new_user($username, $passwordHash, $email);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo "serveur error " . $e->getMessage();
    die();
}