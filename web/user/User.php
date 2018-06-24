<?php

  include dirname(dirname(__DIR__)) . '/config/config.php';
  include dirname(dirname(__DIR__)) . '/wechat/Wx.php';

  class User {
    static $accessToken;
    private $wx;

    public function __construct () {
      $this->wx = new Wx($GLOBALS['config']);
      self::$accessToken = $this->wx->getAccessToken();
      $action = $_GET['a'];
      $query = $_POST;
      echo $this->$action($query);
    }

    private function getUserList () {
      $url = 'https://api.weixin.qq.com/cgi-bin/user/get?access_token='. self::$accessToken .'&next_openid=';
      return $this->wx->curl($url);
    }

    private function getUserDetail ($query) {
      $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='. self::$accessToken .'&openid='. $query['openid'] .'&lang=zh_CN';
      return $this->wx->curl($url);
    }

    private function updateRemak ($query) {
      $url = 'https://api.weixin.qq.com/cgi-bin/user/info/updateremark?access_token=' . self::$accessToken;
      return $this->wx->curl($url, $query);
    }

    private function getBlackList ($query) {
      $url = 'https://api.weixin.qq.com/cgi-bin/tags/members/getblacklist?access_token=' . self::$accessToken;
      return $this->wx->curl($url, $query);
    }

    private function batchBlackUser ($query) {
      $url = 'https://api.weixin.qq.com/cgi-bin/tags/members/batchblacklist?access_token=' . self::$accessToken;
      return $this->wx->curl($url, $query);
    }

    private function batchCancelBalckUser ($query) {
      $url = 'https://api.weixin.qq.com/cgi-bin/tags/members/batchunblacklist?access_token=' . self::$accessToken;
      return $this->wx->curl($url, $query);
    }
  }

  $user = new User();
