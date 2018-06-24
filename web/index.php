<?php

  include dirname(__DIR__) . '/config/config.php';
  include dirname(__DIR__) . '/wechat/Wx.php';

  $wx = new Wx($GLOBALS['config']);
  $sign = $wx->sign();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>微信公众号开发之jssdk</title>
  <style>
    .btn {
      margin-bottom: 30px;
      width: 100px;
      height: 30px;
      line-height: 30px;
      text-align: center;
      font-size: 14px;
      background-color: #2d8cf0;
      color: #fff;
      cursor: pointer;
    }
  </style>
</head>
<body>
  <img src="" id="img" width="50" height="50">
  <div id="choose-img" class="btn">选择图片</div>
  <div id="scanqrcode" class="btn">扫描二维码</div>

  <script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
  <script type="text/javascript" src="http://www.zymseo.com/js/demo.js"></script>
  <script type="text/javascript">
    ;(function () {

      wx.config({
        debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
        appId: '<?php echo $sign["appId"]; ?>', // 必填，公众号的唯一标识
        timestamp: '<?php echo $sign["timestamp"]; ?>', // 必填，生成签名的时间戳
        nonceStr: '<?php echo $sign["nonceStr"]; ?>', // 必填，生成签名的随机串
        signature: '<?php echo $sign["signature"]; ?>',// 必填，签名
        jsApiList: [
          'checkJsApi',
          'onMenuShareTimeline',
          'onMenuShareAppMessage',
          'onMenuShareQQ',
          'onMenuShareQZone',
          'chooseImage',
          'uploadImage',
          'downloadImage',
          'scanQRCode'
        ] // 必填，需要使用的JS接口列表
      });

      wx.ready(function () {
         // config信息验证后会执行ready方法，所有接口调用都必须在config接口获得结果之后，
         // config是一个客户端的异步操作，所以如果需要在页面加载时就调用相关接口，则须把相关接口放在ready函数中调用来确保正确执行。
         // 对于用户触发时才调用的接口，则可以直接调用，不需要放在ready函数中。
          console.log('ready');

          wx.checkJsApi({
              jsApiList: [
                'onMenuShareTimeline',
                'onMenuShareAppMessage',
                'onMenuShareQQ',
                'onMenuShareQZone',
                'chooseImage'
              ], // 需要检测的JS接口列表，所有JS接口列表见附录2,
              success: function(res) {
                console.log(res);
              }
          });

          wx.onMenuShareTimeline({
            title: '微信jssdk测试分享', // 分享标题
            desc: '微信jssdk测试分享',
            link: '<?php echo $result["url"]; ?>', // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
            imgUrl: 'http://www.zymseo.com/wx-public/images/avatar.jpg', // 分享图标
            success: function () {
              console.log('share success');
            },
            error: function () {
              console.log('share error');
            }
          });

          wx.onMenuShareQZone({
            title: '微信jssdk测试到QQ群分享', // 分享标题
            desc: '微信jssdk测试分享', // 分享描述
            link: '<?php echo $result["url"]; ?>', // 分享链接
            imgUrl: 'http://www.zymseo.com/wx-public/images/avatar.jpg', // 分享图标
            success: function () {},
            cancel: function () {}
          });

      });

      $('body')
      .on('click', '#choose-img', function () {
        wx.chooseImage({
          count: 9, // 默认9
          sizeType: ['original', 'compressed'], // 可以指定是原图还是压缩图，默认二者都有
          sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
          success: function (res) {
            var localIds = res.localIds; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
            wx.uploadImage({
              localId: localIds[0].toString(), // 需要上传的图片的本地ID，由chooseImage接口获得
              isShowProgressTips: 1, // 默认为1，显示进度提示
              success: function (res) {
                var serverId = res.serverId; // 返回图片的服务器端ID
                wx.downloadImage({
                  serverId: serverId, // 需要下载的图片的服务器端ID，由uploadImage接口获得
                  isShowProgressTips: 1, // 默认为1，显示进度提示
                  success: function (res) {
                    var localId = res.localId; // 返回图片下载后的本地ID
                    $('#img').attr('src', localId);
                  }
                });
              }
            });
          }
        });
      })
      .on('click', '#scanqrcode', function () {
        wx.scanQRCode({
          needResult: 0, // 默认为0，扫描结果由微信处理，1则直接返回扫描结果，
          scanType: ["qrCode","barCode"], // 可以指定扫二维码还是一维码，默认二者都有
          success: function (res) {
            var result = res.resultStr; // 当needResult 为 1 时，扫码返回的结果
            // alert(result);
          }
        });
      });
    })();
  </script>
</body>
</html>