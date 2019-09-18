<?php
namespace Com;
header("content-type:text/html;charset=utf-8");
class Ajax_Page{
    public $firstRow; // 起始行数
    public $listRows; // 列表每页显示行数
    public $parameter; // 分页跳转时要带的参数
    public $totalRows; // 总行数
    public $totalPages; // 分页总页面数
    public $rollPage   = 3;// 分页栏每页显示的页数
    public $lastSuffix = true; // 最后一页是否显示总页数
    public $css_config=array('margin-left'=>'0px');
    public $is_ajax=false;//是否开启ajax模式
    public $update_dom='';//ajax模式需要更新的页面外层
    public $js_code='';//ajax刷新分页后自动运行的js代码
    public $ajax_url='';
    private $randnumber='';//随机编码
    private $p       = 'p'; //分页参数名
    private $url     = ''; //当前链接URL
    private $nowPage = 1;
    /*每页显示的行数*/
    private $listrow_config=array('10','20','30','40','50','100','200');
    /*内容页订单列表分页样式*/

    // 分页显示定制
    private $config  = array(
        'first'  => '首页',
        'end'    => '末页',
        'prev'   => '上一页',
        'next'   => '下一页',
        'theme'  => '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %GO% %TOTAL_COUNT%',
    );
    
    /**
     * 架构函数
     * @param array $totalRows  总的记录数
     * @param array $listRows  每页显示记录数
     * @param array $parameter  分页跳转的参数
     */
    public function __construct($totalRows, $listRows=20,$ajax=false){
        $this->get_randnumber();//设置样式名字为随机
        $this->is_ajax=$ajax;
        $parameter = array();
        /* 基础设置 */
        $this->totalRows  = $totalRows; //设置总记录数
        $this->listRows   = empty($_POST['listRows_p'])?$listRows:$_POST['listRows_p'];  //设置每页显示行数
        $this->parameter  = empty($parameter) ? $_GET : $parameter;
        $this->nowPage    = empty($_POST[$this->p]) ? 1 : intval($_POST[$this->p]);
        $this->nowPage    = $this->nowPage>0 ? $this->nowPage : 1;
        $this->firstRow   = $this->listRows * ($this->nowPage - 1);
    }
    
    /**
     * 生成随机编码
     * */
    private function get_randnumber(){
        $charset = 'abcdefghkmnprstuvwxyzABCDEFGHKMNPRSTUVWXYZ23456789';
        $_len = strlen($charset)-1;
        for($i=0;$i<5;$i++) {
            $num .= $charset[mt_rand(0,$_len)];
        }
        $this->randnumber=$num;
    }
    
    /**
     * 分页样式组装
     * */
    private function set_css(){
        $css_config=$this->css_config;//获取样式配置
        $randnumber=$this->randnumber;//获取随机编码
        $css="<style>
                .my_page{width:100%;height:30px;float:left;margin-top:5px;background:white}
                .my_page .ul_$randnumber{width:auto;float:left;height:30px;border:1px solid #DDDDDD;border-radius:4px;margin-left:{$css_config['margin-left']}}
                .my_page .ul_$randnumber select{width:auto;height:24px !important;float:left;margin-left:10px;margin-right:10px;margin-top:3px}
                .my_page .ul_$randnumber input{width:40px !important;height:30px !important;border:0;float:left;border-left:1px solid #DDDDDD;}
                .my_page .ul_$randnumber a{display:block;width:auto;height:30px;float:left;padding:0 12px 0 12px;line-height:30px;border-left:1px solid #DDDDDD;color:#99999C}
                .my_page .ul_$randnumber p{width:auto;height:30px;float:left;line-height:30px;padding:0 5px 0 5px}
              </style>";
        return $css;
    }
    
    /**
     * 生成链接URL
     * @param  integer $page 页码
     * @return string
     */
    private function url($page){
        if($this->is_ajax){
            return "javascript:ajax_page($page)";
        }else{
            return str_replace(urlencode('[PAGE]'), $page, $this->url);
        }       
    }
    
    
    /**
     * 获取选择每页显示行数的html
     */
    private function get_listRows_html(){
        //每页显示条数
        $the_listRows="<select class='set_listRows'>";
        foreach($this->listrow_config as $v){
            if($v==$this->listRows){
                $the_listRows.="<option value='$v' selected>$v</option>";
            }else{
                $the_listRows.="<option value='$v'>$v</option>";
            }                    
        }
        $the_listRows.="</select>";
        return $the_listRows;
    }
    
    /**
     * 组装分页链接
     * @return string
     */
    public function show(){
        if(0 == $this->totalRows) return '';  
        /* 生成URL */
        $this->parameter[$this->p] = '[PAGE]';
        $this->url = U(ACTION_NAME, $this->parameter);
        /* 计算分页信息 */
        $this->totalPages = ceil($this->totalRows / $this->listRows); //总页数
        if(!empty($this->totalPages) && $this->nowPage > $this->totalPages){//当前页面大于总页面
            $this->nowPage = $this->totalPages;//当前页面设置为总页面
        }
        
        /* 计算分页临时变量 */
        $now_cool_page      = $this->rollPage/2;
        $now_cool_page_ceil = ceil($now_cool_page);
        $this->lastSuffix && $this->config['count'] = $this->totalPages;

        //上一页
        $up_row  = $this->nowPage - 1;
        $up_page = $up_row > 0 ? "<a class='page_a' data-p=$up_row href='javascript:void(0)'>".$this->config['prev']."</a>":'';        

        //下一页
        $down_row  = $this->nowPage + 1;
        $down_page = ($down_row <= $this->totalPages) ? "<a class='page_a' data-p=$down_row href='javascript:void(0)'>".$this->config['next']."</a>":'';
        
        //第一页
        $the_first = '';
        if($this->totalPages > $this->rollPage && ($this->nowPage - $now_cool_page) >= 1){
            $the_first = "<a class='page_a' data-p=1 href='javascript:void(0)'>".$this->config['first']."</a>";
        }
        
        //最后一页
        $the_end = '';
        if($this->totalPages > $this->rollPage && ($this->nowPage + $now_cool_page) < $this->totalPages){
            $the_end = "<a class='page_a' data-p=$this->totalPages href='javascript:void(0)'>".$this->config['end']."</a>";
        }
        //统计
        $the_count="<p>共".$this->totalPages."页,".$this->totalRows."条数据</p>";
        $the_go="<input name='p' value=''></input><a href='javascript:void(0)' class='go' >GO</a>";
        //获取选择每页行数html
        $the_listRows=$this->get_listRows_html();
        //获取样式
        $css=$this->set_css();
        //数字连接
        $link_page = "";
        for($i = 1; $i <= $this->rollPage; $i++){
            /*计算出页数*/
            if(($this->nowPage - $now_cool_page) <= 0 ){
                $page = $i;
            }else
            if(($this->nowPage + $now_cool_page - 1) >= $this->totalPages){
                $page = $this->totalPages - $this->rollPage + $i;
            }else{
                $page = $this->nowPage - $now_cool_page_ceil + $i;
            }         
            if($page > 0 && $page != $this->nowPage){//页数大于0并且页数不等于当前页面         
                if($page <= $this->totalPages){//当前页数小于或者等于总页数才让其显示
                    $link_page .= "<a class='page_a' data-p=$page href='javascript:void(0)'>".$page."</a>";                            
                }else{
                    break;
                }
            }else         
            if($this->nowPage==$page){
                $link_page .= "<a style='color:white;background:pink' href=''>" . $page . '</a>';
            }  
        }     
        //替换分页内容
        $page_str = str_replace(
                        array('%FIRST%','%UP_PAGE%','%LINK_PAGE%','%DOWN_PAGE%', '%END%','%GO%','%TOTAL_COUNT%'),
                        array($the_first, $up_page, $link_page,$down_page, $the_end,$the_go,$the_count),
                        $this->config['theme']
                     );
        return $css."<div class='my_page'><ul class='ul_{$this->randnumber}'>{$the_listRows}{$page_str}</ul></div>";      
    }
}