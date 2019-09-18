<?php
namespace Home\Model;
use Think\Model;
/**
 * 融资租凭模块业务逻辑
 * @author 幸福无期
 */
class TenderModel extends Model{   
   /**
    * 获取首页融资租凭
    * @return array 返回验证结果
    */
   public function getIndexTender(){
       $tenders = M('Tender_lease')->select();
       foreach ($tenders as $k => $v) {
       	    $tenders[$k]['content'] = html_entity_decode($tenders_[$k]['content'], ENT_QUOTES, 'UTF-8');
       }
       return $tenders;
   }
}