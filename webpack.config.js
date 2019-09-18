var webpack = require('webpack');
var commonsPlugin = new webpack.optimize.CommonsChunkPlugin('common.js');
module.exports = {
    //插件项
    plugins: [
        new webpack.optimize.CommonsChunkPlugin('vendor',  'vendor.js'),
        new webpack.optimize.UglifyJsPlugin({
            compress: {
              warnings: false
            }
        })
        //new ExtractTextPlugin('styles.css')
    ],
    //页面入口文件配置
    entry: {
    	//newsList : './React/components/News/newsList',
    	//newsUpdate : './React/components/News/newsUpdate',
    	//newsAdd : './React/components/News/newsAdd',
        //noticeList : './React/components/News/noticeList',
        //noticeUpdate : './React/components/News/noticeUpdate',
        //noticeAdd : './React/components/News/noticeAdd',
        ruleList : './React/components/News/ruleList',
        //ruleUpdate : './React/components/News/ruleUpdate',
        //ruleAdd : './React/components/News/ruleAdd',
    	
    	//groupBuyList : './React/components/GroupBuy/groupBuyList',
    	//noPassGroupBuyList : './React/components/GroupBuy/noPassGroupBuyList',
    	//checkList : './React/components/GroupBuy/checkList',
    	
    	//checkList : './React/components/Order/checkList',
    	//orderList : './React/components/Order/orderList',
    	
    	//checkList : './React/components/MallOrder/checkList',
    	//orderList : './React/components/MallOrder/orderList',
    	
    	//ruleGroup : './React/components/Auth/ruleGroup',
    	//ruleList : './React/components/Auth/ruleList',
    	
    	//memberList : './React/components/Member/memberList',   	
    	//qualificationList : './React/components/MemberCarded/qualificationList',
    	
    	//goodsList : './React/components/Goods/goodsList',
    	//checkList : './React/components/Goods/checkList',
    	//noPassGoodsList : './React/components/Goods/noPassGoodsList',
    	
    	//goodsList: './React/components/GoodsSeo/goodsList',
    	//goodsList: './React/components/MallGoodsSeo/goodsList',
    	
    	//mallApplicationList : './React/components/Businesses/mallApplicationList',
    	//sellerList : './React/components/Businesses/sellerList',
    	//qualificationList : './React/components/Businesses/qualificationList',
    	//shopDetails : './React/components/Businesses/shopDetails',
    	

    	//goodsList : './React/components/MallGoods/goodsList',    	
    	
    	//adminList : './React/components/Admin/adminList',
    	
    	//integratedList : './React/components/Integrated/integratedList',
    	//checkList : './React/components/Integrated/checkList',
    	
    	//selectList: './React/components/TenderSelect/selectList',
    	//selectAdd : './React/components/TenderSelect/selectAdd',
    	//selectUpdate: './React/components/TenderSelect/selectUpdate',    	
    	
    	//selectList: './React/components/IntegratedSelect/selectList',
    	//selectAdd : './React/components/IntegratedSelect/selectAdd',
    	//selectUpdate: './React/components/IntegratedSelect/selectUpdate',
    	
    	//tenderList : './React/components/Tender/tenderList',
    	
    	//purchaseList : './React/components/Purchase/purchaseList',
    	
    	//selectList: './React/components/PurchaseSelect/selectList',
    	//selectAdd : './React/components/PurchaseSelect/selectAdd',
    	//selectUpdate: './React/components/PurchaseSelect/selectUpdate',    	
        vendor: [
            //'../node_modules/react/dist/react.min',
            './React/js/jquery-1.8.3.min',
            '../node_modules/react-redux/dist/react-redux.min',
            '../node_modules/redux-thunk/dist/redux-thunk.min',
           // '../node_modules/react-dom/dist/react-dom.min'
        ]
    },
    //入口文件输出配置
    output: {
    	//path : './App/js/News/newsList',
    	//path : './App/js/News/newsUpdate',
    	//path : './App/js/News/newsAdd',
        //path : './App/js/News/noticeList',
        //path : './App/js/News/noticeUpdate',
        //path : './App/js/News/noticeAdd',
        path : './App/js/News/ruleList',
        //path : './App/js/News/ruleUpdate',
        //path : './App/js/News/ruleAdd',
    
        //path : './App/js/GroupBuy/groupBuyList',
    	//path : './App/js/GroupBuy/noPassGroupBuyList',
    	//path : './App/js/GroupBuy/checkList',
    	
    	//path : './App/js/Order/checkList',
    	//path : './App/js/Order/orderList',
    	
    	//path: './App/js/MallOrder/checkList',
    	//path: './App/js/MallOrder/orderList',
    	
    	//path: './App/js/Auth/ruleGroup',
    	//path: './App/js/Auth/ruleList', 
    	
    	//path: './App/js/Member/memberList',
    	//path: './App/js/MemberCarded/qualificationList',
    	
    	//path: './App/js/Goods/goodsList',
    	//path: './App/js/Goods/checkList',
    	//path : './App/js/Goods/noPassGoodsList',
    	
    	//path: './App/js/GoodsSeo/goodsList',
    	//path: './App/js/MallGoodsSeo/goodsList',
    	
    	//path: './App/js/Businesses/qualificationList',
        //path: './App/js/Businesses/sellerList',
    	//path: './App/js/Businesses/mallApplicationList',
    	//path: './App/js/Businesses/shopDetails',
    	
        
    	//path: './App/js/MallGoods/goodsList',
    	
    	//path: './App/js/Admin/adminList',
    	
    	//path: './App/js/Integrated/integratedList',
    	//path: './App/js/Integrated/checkList',
    	
    	//path: './App/js/TenderSelect/selectList',
        //path: './App/js/TenderSelect/selectAdd',
        //path: './App/js/TenderSelect/selectUpdate',
    	
    	//path: './App/js/IntegratedSelect/selectList',  	
        //path: './App/js/IntegratedSelect/selectAdd',
        //path: './App/js/IntegratedSelect/selectUpdate',       
        
    	//path: './App/js/Tender/tenderList',
    	
    	//path: './App/js/Purchase/purchaseList',
    	
    	//path: './App/js/PurchaseSelect/selectList',  	
        //path: './App/js/PurchaseSelect/selectAdd',
        //path: './App/js/PurchaseSelect/selectUpdate',     	
        filename: '[name].js'
    },
    module: {
        //加载器配置
        loaders: [
            { test: /\.css$/, loader: 'style-loader!css-loader'},
            { test: /\.js$/, exclude:/node_modules/, loader: 'babel?presets[]=react,presets[]=es2015'},
            //{ test: /\.js$/, loader: 'jsx-loader?harmony' }
        ]
    },
    externals: {
        'react': 'React',
        'react-dom': 'ReactDOM'
    },
    //其它解决方案配置
    resolve: {
        root: 'D:/workproject/webpack/src', //绝对路径
        extensions: ['', '.js', '.json', '.scss'],//自动补齐文件后缀
        devtool:false,
        //定义别名
        alias: {
            Jquery:'./Public/js/jquery-1.8.3.min'
        }
    }
};
