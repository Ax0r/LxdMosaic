<?php
namespace dhope0000\LXDClient\App;

use dhope0000\LXDClient\App\RouteApi;
use dhope0000\LXDClient\App\RouteView;
use dhope0000\LXDClient\App\RouteAssets;
use dhope0000\LXDClient\Tools\User\UserSession;
use dhope0000\LXDClient\Tools\User\LogUserIn;
use dhope0000\LXDClient\Tools\User\ValidateToken;

class RouteController
{
    public function __construct(
        UserSession $userSession,
        LogUserIn $logUserIn,
        RouteApi $routeApi,
        RouteView $routeView,
        RouteAssets $routeAssets,
        ValidateToken $validateToken
    ) {
        $this->validateToken = $validateToken;
        $this->userSession = $userSession;
        $this->logUserIn = $logUserIn;
        $this->routeApi = $routeApi;
        $this->routeView = $routeView;
        $this->routeAssets = $routeAssets;
    }

    public function routeRequest($explodedPath)
    {
        if (isset($explodedPath[0]) && $explodedPath[0] == "api") {
            $headers = getallheaders();

            // PHP-FPM strikes again
            $headers = array_change_key_case($headers);

            if (!isset($headers["userid"]) || !isset($headers["apitoken"])) {
                http_response_code(403);
                echo json_encode(["error"=>"Missing either user id or token"]);
                exit;
            }

            if (!$this->validateToken->validate($headers["userid"], $headers["apitoken"])) {
                http_response_code(403);
                echo json_encode(["error"=>"Not valid token"]);
                exit;
            }

            $this->routeApi->route($explodedPath, $headers["userid"]);
            exit;
        }

        $logoReq = implode("/", $explodedPath) === "assets/lxdMosaic/logo.png";

        $loginSet = isset($_POST["login"]);

        if ($this->userSession->isLoggedIn() !== true && !$loginSet && !$logoReq) {
            http_response_code(403);
            require __DIR__ . "/../../views/login.php";
            exit;
        } elseif ($loginSet) {
            if ($this->logUserIn->login($_POST["username"], $_POST["password"]) !== true) {
                // Should never fire login throws exceptions
                throw new \Exception("Couldn't login", 1);
            }
        } elseif (isset($explodedPath[0]) && $explodedPath[0] == "logout") {
            $this->userSession->logout();
            header("Location: /");
            exit;
        }

        $routesForViewRoute = ["index", "login", "views"];

        if (!isset($explodedPath[0]) || in_array($explodedPath[0], $routesForViewRoute)) {
            $this->routeView->route($explodedPath);
        } elseif ($explodedPath[0] == "assets") {
            $this->routeAssets->route($explodedPath);
        } elseif ($explodedPath[0] == "terminals?cols=80&rows=24") {
            $port = '3000';

            $url = $_SERVER['REQUEST_SCHEME']
            . '://localhost:' . $port
            . $_SERVER['REQUEST_URI'];

            $ch = curl_init();

            //set the url, number of POST vars, POST data
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents('php://input'));
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json'
            ));

            $server_output = curl_exec($ch);
            curl_close($ch);

            echo $server_output;
        } else {
            throw new \Exception("Dont understand the path", 1);
        }

        return true;
    }
}
