<?php
require 'vendor/autoload.php';

use Hojin\Url\App\Redirect;
use Hojin\Url\DS\Article;
use Hojin\Url\DS\Url;
use Hojin\Url\DS\Shop;
use Hojin\Url\Logger\Logger;

// force tls
if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === "off") {
    if (!in_array($_SERVER['HTTP_HOST'],["localhost:8080"])) {
        header('Location: '.'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
        exit;
    }
}

const RESERVED_KEYWORD = ["shop"];

$requestUri = substr($_SERVER["REQUEST_URI"] ?? "/", 1);
(new Logger)->info("index", ["request"=>$_SERVER["REQUEST_URI"]]);
$params = explode("/", $requestUri);
// API GET Route
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    switch ($params[0]) {
        case 'shop':
            echo json_encode((new Shop)->get($_GET['sigungu'], $_GET['lat'], $_GET['lng']));
            exit;
            break;
    }
}

// view
include "src/App/View.php";