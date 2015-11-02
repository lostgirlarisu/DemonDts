<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>电波.me-电波.me 3.0 beta测试版</title>
<meta content="Sociax企业社区" name="keywords">
<meta content="Sociax企业社区" name="description">
<link href="http://localhost/addons/theme/stv1/_static/image/favicon.ico?v=20130505" type="image/x-icon" rel="shortcut icon">
<link href="http://localhost/addons/theme/stv1/_static/css/global.css?v=20130505" rel="stylesheet" type="text/css" />
<link href="http://localhost/addons/theme/stv1/_static/css/module.css?v=20130505" rel="stylesheet" type="text/css" />
<link href="http://localhost/addons/theme/stv1/_static/css/menu.css?v=20130505" rel="stylesheet" type="text/css" />
<link href="http://localhost/addons/theme/stv1/_static/css/form.css?v=20130505" rel="stylesheet" type="text/css" />
<link href="http://localhost/addons/theme/stv1/_static/css/jquery.atwho.css?v=20130505" rel="stylesheet" type="text/css" />
<link href="http://localhost/apps/public/_static/login.css?v=20130505" rel="stylesheet" type="text/css"/><script>
/**
 * 全局变量
 */
var SITE_URL  = 'http://localhost';
var UPLOAD_URL= 'http://localhost/data/upload';
var THEME_URL = 'http://localhost/addons/theme/stv1/_static';
var APPNAME   = 'public';
var MID		  = '0';
var UID		  = '0';
var initNums  =  '140';
var SYS_VERSION = '20130505'
// Js语言变量
var LANG = new Array();
</script>
<script src="http://localhost/data/lang/public_zh-cn.js?v=20130505"></script><script src="http://localhost/addons/theme/stv1/_static/js/jquery-1.7.1.min.js?v=20130505"></script>
<script src="http://localhost/addons/theme/stv1/_static/js/jquery.form.js?v=20130505"></script>
<script src="http://localhost/addons/theme/stv1/_static/js/common.js?v=20130505"></script>
<script src="http://localhost/addons/theme/stv1/_static/js/core.js?v=20130505"></script>
<script src="http://localhost/addons/theme/stv1/_static/js/plugins/core.comment.js?v=20130505"></script>
<script src="http://localhost/addons/theme/stv1/_static/js/module.js?v=20130505"></script>
<script src="http://localhost/addons/theme/stv1/_static/js/module.common.js?v=20130505"></script>
<script src="http://localhost/addons/theme/stv1/_static/js/jwidget_1.0.0.js?v=20130505"></script>
<script src="http://localhost/addons/theme/stv1/_static/js/jquery.atwho.js?v=20130505"></script>
<script src="http://localhost/addons/theme/stv1/_static/js/jquery.caret.js?v=20130505"></script>
<script src="http://localhost/addons/theme/stv1/_static/js/ui.core.js?v=20130505"></script>
<script src="http://localhost/addons/theme/stv1/_static/js/ui.draggable.js?v=20130505"></script>
<link href="http://localhost/addons/plugin/SpaceStyle/html/base.css" rel="stylesheet" type="text/css" /><link href="http://localhost/addons/plugin/SpaceStyle/themes/gray/style.css" rel="stylesheet" type="text/css" /></head>
<body>

<div id="login-bg">
    <div class="login-b" style="opacity:1;">
        <img src="http://localhost/data/upload/2013/0510/21/518cf6f712967.PNG" style="width:100%;height:auto;margin-left:0;opacity:1;visibility:visible;" />
     </div>
     <div id="login-content">
	      <div id="wrap-hd" style="opacity: 1; visibility: visible;">
          <div class="logo" ></div>
          <div class="login-guide"><p>电波.me 3.0 beta测试版</p></div>
          <div class="s-login">
                <form id="ajax_login_form" method="POST" action="http://localhost/index.php?app=public&mod=Passport&act=doLogin">
                <div class="login-bd">
                    <ul class="clearfix" model-node="login_input">
                        <li class="s-row" style="z-index:100">
                          <div class="input">
                             <label class="l-login">邮箱</label>
                             <div>
                                 <input id="account_input" name="login_email" type="text" class="s-txt1" autocomplete="off" />
                                 <div class="txt-list on-changes" style="z-index:999">
                                   <p>请选择或继续输入...</p>
                                   <ul>
                                      <li email="" rel="show"></li>
                                      <li email="" rel="show"></li>                                    </ul>
                                  </div>
                              </div>
                           </div>
                        </li>
                        <li class="s-row">
                          <div class="input">
                            <label class="l-login">密码</label>
                                                        <input id="pwd_input" name="login_password" type="text" class="s-txt1" autocomplete="off" />
                                                      </div>
                        </li>
                        <li class="actionBtn"><a href="javascript:;" onclick="$('#ajax_login_form').submit();" class="btn-login">登录</a></li>
                        <li class="s-row1">
                                                    <a onclick="javascript:window.open('http://localhost/index.php?app=public&mod=Register&act=index','_self')">注册帐号</a>
                                                  </li>
                        <li class="s-row1">
                            <a class="s-f-psd" href="http://localhost/index.php?app=public&mod=Passport&act=findPassword">忘记密码?</a>
                            <a class="auto left" event-node="login_remember" href="javascript:;"><span class="check-ok"><input type="hidden" name="login_remember" value="1" /></span>下次自动登录</a>
                        </li>
                    </ul>
                </div>
                </form>
                <div id="js_login_input" style="display:none" class="error-box"></div>

                <div class="third-party"><dl><dd><a href="http://localhost/index.php?app=public&mod=Widget&act=displayAddons&type=sina&addon=Login&hook=login_sync_other" class="ico-sina"></a></dd><dd><a href="http://localhost/index.php?app=public&mod=Widget&act=displayAddons&type=qzone&addon=Login&hook=login_sync_other" class="ico-qzone"></a></dd><dd><a href="http://localhost/index.php?app=public&mod=Widget&act=displayAddons&type=qq&addon=Login&hook=login_sync_other" class="ico-qq"></a></dd><dd><a href="http://localhost/index.php?app=public&mod=Widget&act=displayAddons&type=renren&addon=Login&hook=login_sync_other" class="ico-renren"></a></dd><dd><a href="http://localhost/index.php?app=public&mod=Widget&act=displayAddons&type=douban&addon=Login&hook=login_sync_other" class="ico-douban"></a></dd><dd><a href="http://localhost/index.php?app=public&mod=Widget&act=displayAddons&type=baidu&addon=Login&hook=login_sync_other" class="ico-baidu"></a></dd><dd><a href="http://localhost/index.php?app=public&mod=Widget&act=displayAddons&type=taobao&addon=Login&hook=login_sync_other" class="ico-taobao"></a></dd></dl></div>          </div>         
        </div>
      </div>
      <div id="footer" style="opacity:1;visibility:visible;margin:0;bottom:0;position:absolute;width:100%;height:50px;z-index:-1">
            <div class="footer-wrap" style="left:50%;margin-left:-300px;position:absolute;top:0;width:560px;border:none">
                <p>©2012 ZhishiSoft All Rights Reserved. </p>
            </div>
      </div>
</div>
<script src="http://localhost/online_check.php?uid=0&uname=&mod=Passport&app=public&act=login&action=trace"></script>
<script src="http://localhost/addons/theme/stv1/_static/js/jquery.form.js" type="text/javascript"></script>
<script src="http://localhost/apps/public/_static/login.js" type="text/javascript"></script>
</body>
</html>