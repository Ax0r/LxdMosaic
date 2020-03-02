<?php

namespace dhope0000\LXDClient\Tools\User;

class UserSession
{
    public function isLoggedIn()
    {
        return isset($_SESSION["userId"]) && !empty($_SESSION["userId"]);
    }

    public function logout()
    {
        $_SESSION = [];
        return true;
    }
}
