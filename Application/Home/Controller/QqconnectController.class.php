<?php

namespace Home\Controller;

use Think\Controller;
use Com\Qqconnect;
class QqconnectController extends Controller {
/* 

* Type类型，初始化

* QQConnet  WeiboConnect 

*/

    public function index(){
	   
    switch ($_GET['type']) {

    /* QQ互联登录 */

    case qq:
        $app_id = C('APP_ID');
        $scope = C('SCOPE');
        $callback = C('CALLBACK');
        $sns = new Qqconnect;

        $login_url = $sns->login($app_id, $callback, $scope);
	    header('Location:'.$login_url);

    break;


    /* 默认无登录 */

    default:

    $this->error("无效的第三方方式",U('Member/login'));

    break;

    }

    }  

      /*    

       * 互联登录返回信息

       * 获取code 和 state状态，查询数据库 

       *  */

 public function callback() {

    switch ($_GET['type']) {

    /* 接受QQ互联登录返回值 */

    case qq:

    empty($_GET['code']) && $this->error("无效的第三方方式",U('Member/login'));

    $app_id = C('APP_ID');

                       $app_key = C('APP_KEY');

                        $callback = C('CALLBACK');

    $qq = new QQConnect;

    /* callback返回openid和access_token */

    $back = $qq->callback($app_id , $app_key, $callback);
	

                        //防止刷新

    empty($back) && $this->error("请重新授权登录",U('Member/login'));

    //此处省略数据库查询，查询返回的$back['openid']


  break;


    default:

    $this->error("无效的第三方方式",U('Member/login'));

    break;

    }

    }

}  