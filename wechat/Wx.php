<?php
	/**
	 * 微信操作基类
	 */
	class Wx {
		static $config = array();
		protected $apiUrl;
		protected $message;
		protected $accessToken;
	
		public function __construct ($config=array()) {
			if (!is_array($config) || empty($config)) return;
			self::$config = $config;
			$this->apiUrl  = 'https://api.weixin.qq.com';
			$this->valid();
			$this->getAccessToken();
			$this->message = $this->parsePostRequestData();
		}
		
		/**
		 * 绑定微信服务器
		 */
		private function valid () {
			if (
				isset($_GET["signature"]) && 
				isset($_GET["timestamp"]) && 
				isset($_GET["nonce"]) && 
				isset($_GET["echostr"])
			) {
				$signature = $_GET["signature"];
				$timestamp = $_GET["timestamp"];
				$nonce = $_GET["nonce"];
				$token = self::$config['token'];

				$tmpArr = array($token, $timestamp, $nonce);
				sort($tmpArr, SORT_STRING);
				
				$tmpStr = implode($tmpArr);
				$tmpStr = sha1($tmpStr);
				
				if ($tmpStr === $signature) {
					echo $_GET["echostr"];
				}
			}
		}

		/**
     * 获取access_token
     * @return string access_token
		 * @author zhaoyiming
     */
    public function getAccessToken () {
      $filename = dirname(__DIR__) . '/cache/token_' . md5(self::$config['appId'] . self::$config['appSecret']) . '.php';

      if (is_file($filename) && filemtime($filename) + 7100 > time()) {
        $data = include $filename;
      } else {
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='. self::$config['appId'] . '&secret=' . self::$config['appSecret'];
        $data = json_decode($this->curl($url), true);
        file_put_contents($filename, "<?php \n return " . var_export($data, true) . "; \n ?>");
      }

      return $data['access_token'];
		}
		
		/**
		 * 获取jsapi_ticket
		 * @return string
     * @author zhaoyiming
		 */
		private function getJsapiTicket () {
			$filename = dirname(__DIR__) . '/cache/ticket_' . md5(self::$config['appId'] . self::$config['appSecret'] . '.php');

			if (is_file($filename) && filemtime($filename) + 7100 > time()) {
				$data = include $filename;
			} else {
				$url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token='. $this->getAccessToken() .'&type=jsapi';
				$data = json_decode($this->curl($url), true);
				file_put_contents($filename, "<?php \n return ". var_export($data, true) ."; \n ?>");
			}

			return $data['ticket'];
		}

		/**
		 * 生成签名
		 */
		public function sign () {
			// 通过4个值来获取签名，分别是随机数，临时票据、时间戳、当前url地址
      $nonceStr = $this->makeRandom();
      $ticket = $this->getJsapiTicket();
      $timestamp = time();
      $url = $_SERVER['REQUEST_SCHEME'] ? $_SERVER['REQUEST_SCHEME'] : 'http' . '://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
			
			// 将以上4个值字典化排序
			$arr = array(
				'noncestr=' . $nonceStr,
        'jsapi_ticket=' . $ticket,
        'timestamp=' . $timestamp,
        'url=' . $url
			);
			sort($arr, SORT_STRING);

			// 将排序后的数组转成url参数拼接的形式
			$str = implode('&', $arr);
			
			// 将参数使用sha1加密生成签名
			$signature = sha1($str);
			
			return array(
        'appId' => self::$config['appId'],
        'timestamp' => $timestamp,
        'nonceStr' => $nonceStr,
        'signature' => $signature,
        'url' => $url
      );
		}

		/**
     * 生成随机字符串
     * @param int $step 随机数步长值
     * @return string $random 生成的随机数 
     * @author zhaoyiming
     */
		protected function makeRandom ($step=16) {
			$seed = '0123456789abcdefghijklmnopqrstuvwxyz';
			$random = '';
			
			for ($i = 0; $i < $step; $i += 1) {
			 $random .= $seed[rand(0, strlen($seed) - 1)]; 
			}

			return $random;
		}

		public function getMessage () {
			return $this->message;
		}
		
		/**
		 * 解析粉丝发来的消息
		 * @return object SimpleXMLElement
		 * @author zhaoyiming
		 */
		private function parsePostRequestData () {
			if (isset($GLOBALS['HTTP_RAW_POST_DATA'])) {
				return simplexml_load_string($GLOBALS['HTTP_RAW_POST_DATA'], 'SimpleXMLElement', LIBXML_NOCDATA);
			}
		}

		/**
     * 服务器之间的通信方法
     * @param string $url 请求地址
     * @param array $data POST请求时的数据
     * @return string
		 * @author zhaoyiming
     */
    public function curl ($url, $data) {
      // 初始化curl
      $ch = curl_init();

      // 设置请求的地址
      curl_setopt($ch, CURLOPT_URL, $url);
      
      // 设置接收返回的数据，不直接展示在页面
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      
      // 设置禁止证书校验
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

      if (!empty($data)) {
        // 设置请求超时时间
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        // 设置开启POST
        curl_setopt($ch, CURLOPT_POST, 1);
        
        // 传递POST数据
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
      }

      // 定义一个空字符串，用来接收请求的结果
      $result = '';
      if (curl_exec($ch)) {
        $result = curl_multi_getcontent($ch);
      }

      curl_close($ch);
      return $result;
    }
	}