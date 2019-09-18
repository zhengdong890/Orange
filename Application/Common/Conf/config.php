<?php
$router = array(   
    '/^fenxiang-(.*)$/'     =>'Home/Goods/goods?goods_id=:1',//商品详情页
    '/^shangpin-(.*)$/'     =>'Goods/goods?goods_id=:1',//商品详情页
    '/^news-(.*)$/'         =>'Home/News/newsDetails?id=:1',//新闻详情
    '/^huhanrobot(.*)$/' => 'Home/Categorys/goodsList?pid=72&cat_id=75',
    '/^mall\/c-(.*)$/'     =>'Home/MallCategorys/goodsList?cat_id=:1',//商品详情页
    '/^shemall\/c-(.*)$/'     =>'Home/Categorys/goodsList?cat_id=:1',//设备出租
    '/^xinwen$/'    =>'Home/News/index',//新闻
    '/^gonggao$/'   =>'Home/News/index2',//公告
    '/^rules$/'     =>'Home/News/index3',//规则
   '/^xinwen-(.*)$/'     =>'Home/News/news_info?id=:1',//新闻详情
    '/^gonggao-(.*)$/'    =>'Home/News/news_info?id=:1',//公告详情
    '/^rules-(.*)$/'      =>'Home/News/news_info?id=:1',//规则详情
    '/^wenti-(.*)$/'      =>'Home/Problem/index?id=:1',//规则详情
    '/^cart$/'    =>'m=Home&c=Cart&a=cartList',//购物车
    '/^member$/'    =>'Home/MemberCenter/member',//个人中心
     '/^order$/'    =>'Home/MemberOrder/orderList',//我的订单
     '/^logout$/'      =>'Home/Member/logout',//注销
     '/^qyrz$/'      =>'Home/MemberCenter/businessQualification',//企业认证
     '/^zhaq$/'      =>'Home/MemberCenter/accountSafety',//账户安全
     '/^zhxx$/'      =>'Home/MemberCenter/memberData',//账户信息
     '/^dzgl$/'      =>'Home/memberAddress/memberAddress',//地址管理
     '/^order$/' =>'Home/MemberOrder/orderList',//我的订单
     '/^gzsp$/'    =>'Home/MemberCenter/favorites',//关注的商品
     '/^balance$/'       =>'Home/MemberCenter/balance',//余额
     '/^myPoints$/'     =>'Home/Member_mypoints/myPoints',//我的积分
     '/^myCoupon$/'     =>'Home/MemberCoupon/myCoupon',//优惠券
     '/^regoods$/'      =>'Home/MemberCenter/returned_goods',//返修退换货
     '/^myInvoice$/'      =>'Home/MemberCenter/myInvoice',//我的发票
     '/^jyjf$/'      =>'Home/MemberCenter/trade_disputes',//交易纠纷
     '/^mffb$/'        =>'Home/Member_goods/selectCategory',//免费发布
     '/^gxsp$/'        =>'Home/Member_goods/goodsList',//共享商品
     '/^gxdd$/'        =>'Home/MemberSellerOrder/orderList',//共享订单
     '/^myshop$/'       =>'Home/MemberCenter/sellerMall',//我的商城
     '/^register$/'        =>'Home/Member/register',//免费注册
     '/^wjmm$/'         =>'Home/Member/forgetPassword',//忘记密码
     '/^login$/'        =>'Home/Member/login',//登入
     '/^wenti$/'        =>'Home/Help/index',//e橙优品常见问题
     '/^zhinan$/'        =>'Home/Help/index',//e橙优品常见问题
     '/^xinwen_(.*)$/'     =>'Home/SeoNews/index?id=:1',//seo 
      '/^goods$/'       =>'Home/MallCategorys/goodsList',//商城商品
      '/^integral$/'       =>'Home/Integralmall/index',//积分商城
      '/^problem$/'       =>'Home/Integralmall/common_problem',//积分常见问题
      '/^record$/'       =>'Home/Integralmall/exchange_record',//积分常见问题
      '/^agreement$/'       =>'Home/Member/agreement',//用户服务协议
      '/^wuliu$/'       =>'Home/ShippingTemplet/logisticsTool',//物流工具
    /*网站导航*/
    '/^robots_index(.*)$/'          => 'Home/Tool/goodsList?pid=43',//工具超市
    '/^sbcz$/'                      => 'Home/EquipmentRent/index',//设备出租
    '/^jinrong$/'                   => 'Home/Server/server',//金融服务
    '/^tuangou$/'                   => 'Home/GroupBuy/index',//品牌闪购
    '/^plcg(.*)$/'                  => 'Home/BulkPurchase/index',//批量采购
    '/^rongzi(.*)$/'                => 'Home/Tender/index',//融资招标
    '/^jcxiangmu(.*)$/'             => 'Home/Integration/index',//集成项目     
    /*下单流程*/  
    '/^MallOrder\/quickBuy$/'       => 'Home/Mall_order/quickBuy',//快速下单
    '/^Alipay\/alipayPayment$/'     => 'Home/Alipay/alipayPayment',//支付宝确认支付处理页
    '/^WechatPay\/createPayCode$/'  => 'Home/WechatPay/createPayCode',//确认支付微信扫描二维码支付处理页
    '/^MallOrder\/offlinePay$/'     => 'Home/Mall_order/setOrdePayModelFour',//快速下单
    /*卖家中心-订单管理*/
    '/^SellerOrder\/getOrderData$/' => 'Home/Seller_order/getOrderData',//获取订单数据接口
    '/^SellerOrder\/SendGoods$/'    => 'Home/Seller_order/SendGoods',//发货页面
    '/^SellerOrder\/SendAllGoods$/' => 'Home/Seller_order/SendAllGoods',//批量发货页面
    /*卖家中心-商品管理*/
    '/^sellerGoods\/getGoodsAndSku$/' => 'Home/sellerGoods/getGoodsAndSku',//获取商品和sku
    /*卖家中心-营销活动管理*/
    '/^ReleaseActivity\/activityAdd$/' => 'Home/ReleaseActivity/activityAdd',//添加活动
    '/^ReleaseActivity\/activityList$/' => 'Home/ReleaseActivity/activityList',//活动列表
    /*卖家中心-店铺分类管理*/
    '/^ShopCategory\/getCategory$/' => 'Home/ShopCategory/getCategory',
);


/*积分*/
$score_config = array(
    'REGISTER' => array(
    	'code'  => 1,
        'name'  => '新用户注册',
        'score' => 300
    ),
    'BUSINESS_APPLICATION' => array(
    	'code'  => 2,
        'name'  => '企业认证',
        'score' => 300
    ),
    'LOGIN' => array(
    	'code'  => 3,
        'name'  => '登陆',
        'score' => 10,
        'time'  => 1,
        'number'=> 1
    ),
    'BUY' => array(
    	'code'  => 4,
        'name'  => '采购',
        'condition' => array(
            '500'   => 300,
            '5000'  => 500,
            '10000' => 10000
        )
    ),  
    'SHOP' => array(
    	'code'  => 5,
        'name'  => '企业开店',
        'score' => 1000
    ), 
    'RECOMMEND_SHOP' => array(
    	'code'  => 6,
        'name'  => '推荐企业开店',
        'score' => 300
    ), 
    'SHARE' => array(
    	'code'  => 7,
        'name'  => '分享',
        'score' => 10,
        'time'  => 1,
        'number'=> 1
    )  
);

return array(
	//'配置项'=>'配置值'
	'USER_AUTH_ON'=>true,//是否开启验证
	'USER_AUTH_TYPE' => 2,//验证类型（1：登录验证2：时时验证）
	'USER_AUTH_KEY' => 'uid', //用户认证识别号
	'NOT_AUTH_CONTROLLER' => 'Index', //无需认证的控制器
	'NOT_AUTH_ACTION' => 'logout', //无需认证的方法
    'APP_GROUP_LIST' => 'Home,Admin,Shop', //分组	
    'DEFAULT_GROUP'  => 'Home', //默认分组
	'APP_GROUP_MODE'=>1,
    'DB_TYPE'=>'mysql',
    'SCORE_CONFIG'  => $score_config,
	//数据库配置
    'DB_HOST'   => '127.0.0.1', // 服务器地址
	'DB_NAME'   => 'orange', // 数据库名
	'DB_PORT'   => '3306', // 端口
	'DB_USER'   => 'root',// 用户名
	'DB_PWD'    => 'Chengcheng83225308!@', // 密码	
	'DB_PREFIX' => 'tp_', // 数据库表前缀 
    'TOKEN' => 'weixin',
    'APPID' => 'wx9d633204f65174d0',
    'APPSECRET'=>'cd91ecae9428fdcd42dbbb4a49980b6d',
    'WENJIANNAME' => '',
    'URL_MODEL'   => 0,
    'URL_ROUTER_ON'  => true,//开启路由
    'URL_ROUTE_RULES'=>$router,
    'URL_CASE_INSENSITIVE'=>false,//设置debug在关闭的时候，生成的url变成小写的问题
    'SHOW_ERROR_MSG' =>  true,
    'ERROR_PAGE' => 'http://www.orangesha.com/Home/Error/error_404.html',//错误页面
    /*密保问题*/
    'SECURITY' => array(
        '1'=>'您母亲的姓名是？',
        '2'=>'您父亲的姓名是？',
        '3'=>'您配偶的姓名是？',
        '4'=>'您的出生地是？',
        '5'=>'您高中班主任的名字是？',
        '6'=>'您小学班主任的名字是？',
        '7'=>'您的小学校名是？您的学号（或工号）是？',
        '8'=>'您父亲的生日是？',
        '9'=>'您母亲的生日是？',
        '10'=>'您配偶的生日是？',
        '11'=>'对您影响最大的人名字是？'
    ),
    'SESSION_OPTIONS'=>array('domain'=>'orangesha.com'),//session配置
    /*二级域名定向*/
    'APP_SUB_DOMAIN_DEPLOY'   => 1, // 开启子域名配置
    'APP_SUB_DOMAIN_RULES'    => array(
        'houtai' => array('Admin'),
        '*'      => array('Shop') // 二级泛域名指向Shop模块
    ),
    'LOG_RECORD' => true,
    //'SHOW_PAGE_TRACE'=>'true',
	//'URL_MODEL' =>2,	
    'STATIC_URL' => 'http://static.orangesha.com',
    'STATIC_URL_QILIU' => 'http://ojaxax8qq.bkt.clouddn.com',	
    'ORANGESHA_URL' =>'http://www.orangesha.com'
);