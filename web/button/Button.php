<?php

include dirname(dirname(__DIR__)) . '/config/config.php';
include dirname(dirname(__DIR__)) . '/wechat/Wx.php';

class Button {
  static $accessToken;
  private $wx;

  public function __construct () {
    $this->wx = new Wx($GLOBALS['config']);
    self::$accessToken = $this->wx->getAccessToken();
    $action = $_GET['a'];
    $query = $_POST;
    echo $this->$action($query);
  }

  private function selectButton () {
    $url = 'https://api.weixin.qq.com/cgi-bin/menu/get?access_token='. self::$accessToken;
    return $this->wx->curl($url);
  }

  private function createButton ($query) {
    $json= <<<php
		  {
		     "button": [
		      {
            "type":"view",
            "name":"我的博客",
            "url":"https://github.com/zhaoyiming0803"
          },
          {    
            "type":"click",
            "name":"点我",
            "key":"haha"
          },
		      {
            "name":"菜单",
            "sub_button":[
              {
                  "type":"view",
                  "name":"百度",
                  "url":"http://www.baidu.com/"
              },
              {
                  "type":"view",
                  "name":"阿里巴巴",
                  "url":"http://www.1688.com/"
              },
              {
                  "type":"view",
                  "name":"腾讯",
                  "url":"http://www.qq.com"
              }
            ]
          }
        ]
      }
php;

    $url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token=' . self::$accessToken;
    return $this->wx->curl($url, $json);
  }

  private function delButton () {
    $url = 'https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=' . self::$accessToken;
    return $this->wx->curl($url);
  }
}

$button = new Button();