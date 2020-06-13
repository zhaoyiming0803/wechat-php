<?php

  include_once dirname(__DIR__) . '/Wx.php';

  /**
   * 专门处理微信消息
   */
  class Message extends Wx {
    static $msg;

    public function __construct ($msg) {
      self::$msg = $msg;
      if (self::$msg->MsgType == 'event') {
        $this->event();
      } else {
        $this->normalMessage();
      }
    }

    /**
     * 用户发送的普通消息
     */
    private function normalMessage () {
      $content = '您好，您刚刚发给我';
      switch (self::$msg->MsgType) {
        case 'text':
          $content .= '一段文字';
          break;
        case 'image':
          $content .= '一张图片';
          break;
        case 'voice':
          $content .= '一段语音';
          break;
        case 'video':
          $content .= '一段视频';
          break;
        case 'shortvideo':
          $content .= '一段短视频';
          break;
        case 'location':
          $content .= '一个地理位置';
          break;
        case 'link':
          $content .= '一条链接';
          break;
        default:
          return;
      }
      $content .= '，当前公众号仅是测试使用，有问题请直接加我微信：zhao-seo';
      $this->text($content);
    }

    /**
     * 事件类消息
     */
    private function event () {
      if (self::$msg->Event == 'subscribe') {
        $content = '感谢您的订阅，当前公众号正在开发中（https://github.com/zhaoyiming0803/wx-public），有问题请直接加我微信：zhao-seo，';
      } else if (self::$msg->Event == 'unsubscribe') {
        $content = '取消订阅';
      } else if (self::$msg->Event == 'LOCATION') {
        $content = '您当前地理位置：维度：' . self::$msg->Latitude . '，经度：' . self::$msg->Longitude;
      } else if (self::$msg->EventKey == 'haha') {
        $content = '当前公众号正在开发中，更多自定义菜单功能，尽情期待^_^';
      } else {
        $content = '欢迎来到我的测试公众号，当前公众号正在开发中（https://github.com/zhaoyiming0803/wx-public）,有问题可以加我微信：zhao-seo';
      }
      $this->text($content);
    }

    /**
     * 被动回复用户消息
     */
    private function text ($content) {
      // 这里的MsgType全部都是text，还可以是其他回复类型，参考api：https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140543
      $returnMsg = '
				<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[text]]></MsgType>
					<Content><![CDATA[%s]]></Content>
				</xml>
      ';
      
      echo sprintf($returnMsg, self::$msg->FromUserName, self::$msg->ToUserName, time(), $content);
    }
  }