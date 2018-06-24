<?php

	include dirname(__DIR__) . '/config/config.php';
	include dirname(__DIR__) . '/wechat/Wx.php';
	
	/**
	* 相关配置及初始化Wx类
	*/
	class Entry {
		protected $wx;

		public function __construct () {
			$this->wx = new Wx($GLOBALS['config']);
		}

		public function handler () {
			$message = $this->wx->getMessage();
			file_put_contents('a.php', var_export($message, true));

			include_once dirname(__DIR__) . '/web/message/Message.php';
			new Message($message);
		}
	}