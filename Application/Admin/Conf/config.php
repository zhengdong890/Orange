<?php
/*后台左侧菜单配置*/
$left_menu_config = array(
    array(
        'name'  => '共享商品管理','title' => 'gxspgl',
        'child' => array(
            array('name' => '添加商品' , 'c' => 'Goods' , 'a' => 'selectcategory'),
            array('name' => '商品列表' , 'c' => 'Goods' , 'a' => 'goodsList' ),
            array('name' => '未通过审核的商品' , 'c' => 'GoodsCheck' , 'a' => 'noPassGoodsList'),
            array('name' => '商品审核' , 'c' => 'GoodsCheck' , 'a' => 'checkList'),
            array('name' => '商品分类' , 'c' => 'Category' , 'a' => 'categoryList'),
            array('name' => '添加商品分类' , 'c' => 'Category' , 'a' => 'categoryAdd'),
            array('name' => '商品类型' , 'c' => 'Goodstype' , 'a' => 'goods_type'),
            array('name' => '加入推荐' , 'c' => 'Goods_model' , 'a' => 'modelList'),
            array('name' => '添加加入推荐' , 'c' => 'Goods_model' , 'a' => 'modelAdd'),
        )
    ), 
    array(
        'name'  => '商城商品分类管理','title' => 'scspgl',
        'child' => array(
        	array('name' => '添加商品分类' , 'c' => 'Mall_category' , 'a' => 'categoryAdd', 'target'=>'_target'),
            array('name' => '商品分类' , 'c' => 'Mall_category' , 'a' => 'categoryList', 'target'=>'_target'),           
            array('name' => '分类属性' , 'c' => 'Mall_category' , 'a' => 'categoryAttrList', 'target'=>'_target') 
        )
    ),
    array(
        'name'  => '商城商品管理','title' => 'scspgl',
        'child' => array(
            array('name' => '商品列表' , 'c' => 'MallGoods' , 'a' => 'goodsList'),
            array('name' => '添加商品' , 'c' => 'MallGoods' , 'a' => 'selectcategory'),
            array('name' => '加入推荐' , 'c' => 'Mall_goods_model' , 'a' => 'modelList'),
            array('name' => '添加加入推荐' , 'c' => 'Mall_goods_model' , 'a' => 'modelAdd'),
            array('name' => '广告位列表' , 'c' => 'Ad' , 'a' => 'adList'),
            array('name' => '添加广告位' , 'c' => 'Ad' , 'a' => 'goodsAdAdd')
        )
    ),
    /*array(
        'name'  => '商品类型管理','title' => 'splxgl',
        'child' => array(
            array('name' => '类型列表' , 'c' => 'GoodsType' , 'a' => 'goodsTypeList'),
            array('name' => '添加类型' , 'c' => 'GoodsType' , 'a' => 'goodsTypeAdd')
        )
    ),*/    
    array(
        'name'   =>'积分商城管理','title'=>'integration',
        'child'  => array(
            array('name'=>'商品列表','c'=>'IntegrationGoods','a'=>'goodsList'),
            array('name'=>'商品添加','c'=>'IntegrationGoods','a'=>'goodsAdd'),
        )
    ),
    array(
        'name'  => '商品品牌管理','title' => 'spppgl',
        'child' => array(
            array('name' => '品牌列表' , 'c' => 'Goods_brand' , 'a' => 'brandList'),
            array('name' => '添加品牌' , 'c' => 'Goods_brand' , 'a' => 'brandAdd'),
            array('name' => '品牌申请列表' , 'c' => 'BrandApplication' , 'a' => 'brandList')
        )
    ),
    array(
        'name'  => '活动管理','title' => 'hdgl',
        'child' => array(
            array('name' => '优惠券列表' , 'c' => 'Coupons' , 'a' => 'couponList'),
            array('name' => '添加优惠券' , 'c' => 'Coupons' , 'a' => 'couponAdd')
        )
    ),
    array(
        'name'  => '团购管理','title' => 'tggl',
        'child' => array(
            array('name' => '商品列表' , 'c' => 'GroupBuy' , 'a' => 'groupBuyList'),
            array('name' => '审核未通过商品' , 'c' => 'GroupBuy' , 'a' => 'noPassGroupBuyList'),
            array('name' => '申请列表' , 'c' => 'GroupBuy' , 'a' => 'checkList')
        )
    ),
    array(
        'name'  => '招标管理','title' => 'zbgl',
        'child' => array(
            array('name' => '集成项目公司' , 'c' => 'Integrated' , 'a' => 'companyList'),
            array('name' => '新增集成项目公司' , 'c' => 'Integrated' , 'a' => 'companyAdd'),
            array('name' => '集成项目列表' , 'c' => 'Integrated' , 'a' => 'integratedList'),
            array('name' => '集成项目审核' , 'c' => 'Integrated' , 'a' => 'checkList'),
            array('name' => '集成项目banner' , 'c' => 'Integrated' , 'a' => 'integratedBannerList'),
            array('name' => '新增集成项目banner' , 'c' => 'Integrated' , 'a' => 'integratedBannerAdd'),
            array('name' => '中标集成项目' , 'c' => 'IntegratedSelect' , 'a' => 'selectList'),
            array('name' => '添加中标集成项目' , 'c' => 'IntegratedSelect' , 'a' => 'selectAdd'),
            array('name' => '融资租凭公司' , 'c' => 'Tender' , 'a' => 'companyList' ,'css' => 'border-top=>1px solid black'),                      
            array('name' => '新增融资租凭公司' , 'c' => 'Tender' , 'a' => 'companyAdd'),
            array('name' => '融资租凭项目列表' , 'c' => 'Tender' , 'a' => 'tenderList'),
            array('name' => '融资租凭banner' , 'c' => 'Tender' , 'a' => 'tenderBannerList'),
            array('name' => '新增融资租凭banner' , 'c' => 'Tender' , 'a' => 'tenderBannerAdd'),
            array('name' => '中标融资租凭' , 'c' => 'TenderSelect' , 'a' => 'selectList'),
            array('name' => '添加中标融资租凭' , 'c' => 'TenderSelect' , 'a' => 'selectAdd'),
            array('name' => '公司类型' , 'c' => 'Company' , 'a' => 'typeList'),
            array('name' => '新增公司类型' , 'c' => 'Company' , 'a' => 'typeAdd'),
            array('name' => '品牌' , 'c' => 'Company' , 'a' => 'brandList'),
            array('name' => '新增品牌' , 'c' => 'Company' , 'a' => 'brandAdd')
        )
    ),
    array(
        'name'  => '批量采购','title' => 'plcg',
        'child' => array(
            array('name' => '批量采购公司' , 'c' => 'Purchase' , 'a' => 'companyList'),
            array('name' => '新增批量采购公司' , 'c' => 'Purchase' , 'a' => 'companyAdd'),
            array('name' => '批量采购列表' , 'c' => 'Purchase' , 'a' => 'purchaseList'),
            array('name' => '立即报价列表' , 'c' => 'Purchase_offer' , 'a' => 'offerList'),
            array('name' => '批量采购banner' , 'c' => 'Purchase' , 'a' => 'purchaseBannerList'),
            array('name' => '新增批量采购banner' , 'c' => 'Purchase' , 'a' => 'purchaseBannerAdd'),
            array('name' => '中标批量采购' , 'c' => 'PurchaseSelect' , 'a' => 'selectList'),
            array('name' => '添加中标批量采购' , 'c' => 'PurchaseSelect' , 'a' => 'selectAdd')
        )
    ),    
    array(
        'name'  => '共享订单','title' => 'gxdd',
        'child' => array(
            array('name' => '订单列表' , 'c' => 'Order' , 'a' => 'orderList'),
            array('name' => '审核订单' , 'c' => 'Order' , 'a' => 'checkList')
        )
    ),
    array(
        'name'  => '商城订单','title' => 'scdd',
        'child' => array(
            array('name' => '订单列表' , 'c' => 'MallOrder' , 'a' => 'orderList' , 'target'=>'_target'),
            array('name' => '审核订单' , 'c' => 'MallOrder' , 'a' => 'checkList'),
            array('name' => '公对公转账' , 'c' => 'MallOrder' , 'a' => 'publicOrderList' , 'target'=>'_target')
        )
    ),
    array(
        'name'  => '站长管理','title' => 'zzgl',
        'child' => array(
            array('name' => '站长统计推广' , 'url' => 'http=>//tongji.baidu.com'),
        )
    ),
    array(
        'name'  => '商户管理','title' => 'shgl',
        'child' => array(
            array('name' => '企业认证申请列表' , 'c' => 'BusinessesApplication' , 'a' => 'qualificationList'),
            array('name' => '商城申请列表' , 'c' => 'MallApplication' , 'a' => 'mallApplicationList'),
            array('name' => '商户列表' , 'c' => 'ShopData' , 'a' => 'sellerList')
        )
    ),
    array(
        'name'  => '会员管理','title' => 'hygl',
        'child' => array(
            array('name' => '注册会员列表' , 'c' => 'Member' , 'a' => 'memberList'),
            array('name' => '身份认证申请' , 'c' => 'MemberCarded' , 'a' => 'qualificationList')
        )
    ),
    array(
        'name'  => '权限管理','title' => 'qxgl',
        'child' => array(
            array('name' => '规则列表' , 'c' => 'Auth' , 'a' => 'ruleList'),
            array('name' => '分组列表' , 'c' => 'AuthGroup' , 'a' => 'ruleGroup'),
            array('name' => '管理员列表' , 'c' => 'Admin' , 'a' => 'adminList')
        )
    ),
    array(
        'name'  => 'SEO管理','title' => 'seogl',
        'child' => array(
            array('name' => '导航seo' , 'c' => 'Seo' , 'a' => 'navList'),         
            //array('name' => '商品分类' , 'c' => 'Seo' , 'a' => 'categoryList'),
            array('name' => '商城商品分类' , 'c' => 'MallCategorySeo' , 'a' => 'categoryList'),
            //array('name' => '共享商品seo' , 'c' => 'GoodsSeo' , 'a' => 'goodsList'),
            array('name' => '商城商品seo' , 'c' => 'MallGoodsSeo' , 'a' => 'goodsList'),
            array('name' => '新闻seo', 'c' => 'Goods_news' , 'a' =>'news')
        )
    ), 
    array(
        'name'  => '系统设置','title' => 'xtsz',
        'child' => array(
            array('name' => '导航列表' , 'c' => 'Nav' , 'a' => 'navList'),
            array('name' => '添加导航' , 'c' => 'Nav' , 'a' => 'navAdd'),
            array('name' => '修改密码' , 'c' => 'Admin' , 'a' => 'passwordUpdate'),
            array('name' => '友情链接' , 'c' => 'Friendlink' , 'a' => 'index'),
            array('name' => '快递公司' , 'c' => 'KuaiDi' , 'a' => 'index')
        )
    ),
    array(
        'name'  => 'Banner设置','title' => 'bannersz',
        'child' => array(
            array('name' => '商城banner' , 'c' => 'Banner' , 'a' => 'mallBannerList'),
            array('name' => '出租banner' , 'c' => 'Banner' , 'a' => 'bannerList')
        )
    ),
    array(
        'name'  => '新闻,公告,规则','title' => 'xwzx',
        'child' => array(
            array('name' => '新闻列表' , 'c' => 'News' , 'a' => 'newsList'),
            array('name' => '添加新闻' , 'c' => 'News' , 'a' => 'newsAdd'),
            array('name' => '公告列表' , 'c' => 'News' , 'a' => 'noticeList'),
            array('name' => '添加公告' , 'c' => 'News' , 'a' => 'noticeAdd'),
            array('name' => '规则列表' , 'c' => 'News' , 'a' => 'ruleList'),
            array('name' => '添加规则' , 'c' => 'News' , 'a' => 'ruleAdd'),
            array('name' => '商户规则列表' , 'c' => 'SellerRule' , 'a' => 'categoryList'),
            array('name' => '添加商户规则' , 'c' => 'SellerRule' , 'a' => 'categoryAdd')
        )
    ),   
    array(
        'name'  => '帮助','title' => 'bz',
        'child' => array(
            array('name' => '帮助列表' , 'c' => 'HelpCategory' , 'a' => 'categoryList'),
            array('name' => '添加帮助' , 'c' => 'HelpCategory' , 'a' => 'categoryAdd')     

        )
    )
);

$order_msg = array(
    'status' => array(
        '-1' => '已取消',
        '0'  => '未审核',
        '1'  => '已审核',
        '2'  => '已完成'
    ),
    'pay_status' => array(
        '0' => '未支付',
        '1' => '已支付' 
    ),
    'send_status' => array(
        '0' => '未发货',
        '1' => '已发货',
        '2' => '已发货 已收货' 
    ),
    'pay_model' => array(
        '1' => '支付宝',
        '2' => '微信支付',
        '3' => '银联',
        '4' => '线下转账' 
    ),  
    'service_type' => array(
        '1' => '退款',
        '2' => '换货',
        '3' => '退款退货'
    ),
    'service_status' => array(
        '-11' => '商家不同意退款',
        '11'  => '申请退款',
        '12'  => '商家同意申请退款',
        '13'  => '已退款',
        '-21' => '商家不同意换货 ',
        '21'  => '申请换货 ',
        '22'  => '商家同意申请换货 ',
        '23'  => '商家已重新发货',
        '-31' => '商家不同意退款退货',
        '31'  => '申请退款退货',
        '32'  => '商家同意退款退货',
        '33'  => '已退款',
    )  
);
return array(
	'ORDER_MSG' => $order_msg,
	//'配置项'=>'配置值'
	'RBAC_SUPERADMIN' =>'admin',     //超级管理员名称
	'ADMIN_AUTH_KEY' => 'superadmin',//超级管理员识别
	'USER_AUTH_ON'=>true,//是否开启验证
	'USER_AUTH_TYPE' => 2,//验证类型（1：登录验证2：时时验证）
	'USER_AUTH_KEY' => 'uid', //用户认证识别号
	'NOT_AUTH_CONTROLLER' => 'Index', //无需认证的控制器
	'NOT_AUTH_ACTION' => 'logout', //无需认证的方法	
	'SESSION_OPTIONS' =>array('use_only_cookies'=>0,'use_trans_sid'=>1),
    'LEFT_MENU' => $left_menu_config,
    'HTTP_CACHE_CONTROL' => 'no-cache',
);