<?php
namespace dhope0000\LXDClient\App;

use dhope0000\LXDClient\App\RouteApi;
use dhope0000\LXDClient\App\RouteView;
use dhope0000\LXDClient\App\RouteAssets;

class RouteController
{
    public function __construct(
        RouteApi $routeApi,
        RouteView $routeView,
        RouteAssets $routeAssets
    ) {
        $this->routeApi = $routeApi;
        $this->routeView = $routeView;
        $this->routeAssets = $routeAssets;
    }

    public function routeRequest($explodedPath)
    {
        if (!isset($explodedPath[0]) || (
                $explodedPath[0] == "index" ||
                $explodedPath[0] == "views"
        )) {
            $this->routeView->route($explodedPath);
        } elseif ($explodedPath[0] == "api") {
            $this->routeApi->route($explodedPath, $_POST);
        } elseif ($explodedPath[0] == "assets") {
            $this->routeAssets->route($explodedPath);

        } elseif ($explodedPath[0] == "terminals?cols=80&rows=24") {
            $port = '3000';

            $url = $_SERVER['REQUEST_SCHEME']
            . '://localhost:' . $port
            . $_SERVER['REQUEST_URI'];

            $ch = curl_init();

            //set the url, number of POST vars, POST data
            curl_setopt($ch,CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "");
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $server_output = curl_exec($ch);
            curl_close ($ch);

            echo $server_output;

        } else {
            throw new \Exception("Dont understand the path", 1);
        }

        return true;
    }
}
