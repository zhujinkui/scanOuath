<?php
header("Content-Type: Text/Html;Charset=UTF-8");
require "./vendor/autoload.php";

$oauth = new \think\Oauth($appid, $sessionKey);

if (!isset($_GET["code"])){
    $redirect_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    $jumpurl = $oauth->getOauthRedirect($redirect_url, "123");
    Header("Location: $jumpurl");
}else{
    $oauth2_info = $oauth->checkOauthAccessToken($_GET["code"]);
    $userinfo = $oauth->getUserInfo($oauth2_info['access_token'], $oauth2_info['openid']);
    echo '<pre/>';
    print_r($userinfo);
}