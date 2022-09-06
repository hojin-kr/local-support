<?php

namespace Hojin\Url\App;

use Hojin\Url\Logger\Logger;

class Redirect
{
    function Redirect(string $url)
    {
        (new Logger)->info("redirect", ["url"=>$url]);
        header("Location: $url");
    }

    public function isRedirect(string $requestUri) : bool
    {
        if (explode("/", $requestUri)[0] != "") {
            return true;
        }
        return false;
    }
}