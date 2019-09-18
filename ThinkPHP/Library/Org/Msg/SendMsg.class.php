<?php 
namespace Org\Msg;
include "TopSdk.php";
use TopClient;
use AlibabaAliqinFcSmsNumSendRequest;
class SendMsg {
    public function send($sms_param , $rec_num , $sign_name , $template_code){ 
        $config = C('msg_config');
        date_default_timezone_set('Asia/Shanghai'); 
        $c = new TopClient;
        $c->appkey = $config['appkey'];
        $c->secretKey = $config['appsecret'];       
        $req = new AlibabaAliqinFcSmsNumSendRequest;
        $req->setExtend("123456");
        $req->setSmsType("normal");
        $req->setSmsFreeSignName($sign_name);
        $req->setSmsParam($sms_param);
        $req->setRecNum($rec_num);
        $req->setSmsTemplateCode($template_code);
        $r = $c->execute($req);
        $r = (array)$r;
        $r['result'] = (array)$r['result'];
        return $r;
    }
}