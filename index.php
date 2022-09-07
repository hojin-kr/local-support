<?php
require 'vendor/autoload.php';

use Hojin\Url\DS\Shop;
use Hojin\Url\Logger\Logger;

// force tls
if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === "off") {
    if (!in_array($_SERVER['HTTP_HOST'],["localhost:8080", "localhost"])) {
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
        case 'slack':
            $ch = curl_init("https://hooks.slack.com/services/");
            $data = "payload=".json_encode([
                "text" => $_GET['msg'],
            ]);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            curl_close($ch);
            (new Logger)->info("slack", ["msg"=>$_GET['msg']]);
            exit;
            break;
    }
}

// view
include "src/App/View.php";