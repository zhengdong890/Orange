<? 

class AlipayTradeRefund{
	private $request_url = 'https://openapi.alipay.com/gateway.do';
    
    private $config = array(
        'app_id'    => '',
        'method'    => 'alipay.trade.refund',
        'format'    => 'JSON',
        'charset'   => 'utf-8',
        'sign_type' => 'RSA2',
        'sign'      => '',
        'timestamp' => date('Y-m-d H:i:s'),
        'version'   => '1.0',
        'app_auth_token' => '',
        'biz_content' => ''
    );

    public function __construct($config = array()){
              
    }

    public function getRequestUrl(){

    }

    public function httpRequest(){
        $options = array(
            CURLOPT_URL            => $this->url, //请求地址
            CURLOPT_HTTPHEADER     => $this->getHeader(), //头部构造
            CURLOPT_HEADER         => 1, //非0值表示输出会包含头部信息
            CURLOPT_RETURNTRANSFER => $this->returntransfer, //返回的数据是否自动显示
            CURLOPT_ENCODING       => 'gzip',//gzip解码
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_CONNECTTIMEOUT => 0
        );        
        $ch       = curl_init();//初始化curl                
        curl_setopt_array($ch, $options); //批量设置参数
        $response = curl_exec($ch); //执行并获取HTML文档内容
        var_dump($response);
        
        //return $body;
    }
}