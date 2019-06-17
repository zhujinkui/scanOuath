<?php
// 类库名称：微信扫码登录
// +----------------------------------------------------------------------
// | PHP version 5.6+
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.myzy.com.cn, All rights reserved.
// +----------------------------------------------------------------------
// | Author: 阶级娃儿 <262877348@qq.com> 群：304104682
// +----------------------------------------------------------------------
namespace think;

class Oauth
{
    // var $app_id = "wxb69d890d597e206a";
    // var $app_secret = "e55aef39acf9ad1f7415d83980b75e2f";
    // var $app_id = "wxb2b4c1ac8ba09041";
    // var $app_secret = "b1afa0d4af88feac2c36976e0d40cbaf";
    /**
     * [__construct 构造函数]
     * @param [type] $app_id     [app_id]
     * @param [type] $app_secret [app_secret]
     */
    public function __construct($app_id = NULL, $app_secret = NULL)
    {
        if($app_id && $app_secret){
            $this->appid     = $app_id;
            $this->appsecret = $app_secret;
        }

        //扫码登录不需要该Access Token, 语义理解需要
        //1. 本地写入
        // $res                = file_get_contents('access_token.json');
        // $result             = json_decode($res, true);
        // $this->expires_time = $result["expires_time"];
        // $this->access_token = $result["access_token"];

        // if (time() > ($this->expires_time + 3600)){
        //     $url                = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->appid."&secret=".$this->appsecret;
        //     $res                = $this->http_request($url);
        //     $result             = json_decode($res, true);
        //     $this->access_token = $result["access_token"];
        //     $this->expires_time = time();
        //     file_put_contents('access_token.json', '{"access_token": "'.$this->access_token.'", "expires_time": '.$this->expires_time.'}');
        // }
    }

    /**
     * [getOauthRedirect 生成扫码登录的URL]
     * @param  [type] $redirect_url [授权后重定向的回调链接地址， 请使用 urlEncode 对链接进行处理]
     * @param  [type] $scope        [应用授权作用域，snsapi_base （不弹出授权页面，直接跳转，只能获取用户openid），snsapi_userinfo （弹出授权页面，可通过openid拿到昵称、性别、所在地。并且， 即使在未关注的情况下，只要用户授权，也能获取其信息 ]
     * @param  [type] $state        [重定向后会带上state参数，开发者可以填写a-zA-Z0-9的参数值，最多128字节]
     */
    public function getOauthRedirect($redirect_url, $state = '', $scope = 'snsapi_login')
    {
        $redirect_uri = urlencode($redirect_url);
        return "https://open.weixin.qq.com/connect/qrconnect?appid=".$this->appid."&redirect_uri=".$redirect_uri."&response_type=code&scope=".$scope."&state=".$state."#wechat_redirect";
    }

    /**
     * [checkOauthAccessToken 检验授权凭证（access_token）是否有效]
     * @param  [type] $code [通过 code 获取 AccessToken 和 openid]
     */
    public function checkOauthAccessToken($code)
    {
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$this->appid."&secret=".$this->appsecret."&code=".$code."&grant_type=authorization_code";
        $res = $this->http_request($url);
        return json_decode($res, true);
    }

    /**
     * [getUserInfo 获取用户基本信息（OAuth2 授权的 Access Token 获取 未关注用户，Access Token为临时获取]
     * @param  [type] $access_token [网页授权接口调用凭证,注意：此access_token与基础支持的access_token不同]
     * @param  [type] $openid       [用户的唯一标识]
     */
    public function getUserInfo($access_token, $openid)
    {
        $url = "https://api.weixin.qq.com/sns/userinfo?access_token=".$access_token."&openid=".$openid."&lang=zh_CN";
        $res = $this->http_request($url);
        return json_decode($res, true);
    }

    /**
     * [http_request HTTP请求（支持HTTP/HTTPS，支持GET/POST）]
     * @param  [type] $url  [请求地址]
     * @param  [type] $data [传输数据]
     */
    protected function http_request($url, $data = null)
    {
        $curl   = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }
}
