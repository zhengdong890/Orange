<?php
function getdicheng(&$data,$id=0){
	if(!isset($data['odl'])){
		$data=array('new'=>array(),'odl'=>$data,'end'=>array(),'flag'=>array());
	}
	foreach ($data['odl'] as $k => $v){
		if($v['pid']==$id){
			$data['new'][]=$v;
			unset($data['odl'][$k]);
			getdicheng($data,$v['id']);
		}
	}
	$len=count($data['new']);
	$data['end'][]=$data['new'][$len-1];
	return $data['end'];
}

function getTreeid($data,$id=0){
	if(!isset($data['odl'])){
		$data=array('new'=>array(),'odl'=>$data);
	}
	foreach ($data['odl'] as $k => $v){
		if($v['pid']==$id){
			$data['new'][]=$v['id'];
			unset($data['odl'][$k]);
			getTreeid($data,$v['id']);
		}
	}
	return $data['new'];
}
function removeXSS($val)
{
	// 实现了一个单例模式，这个函数调用多次时只有第一次调用时生成了一个对象之后再调用使用的是第一次生成的对象（只生成了一个对象），使性能更好
	static $obj = null;
	if($obj === null)
	{
		require('./HTMLPurifier/HTMLPurifier.includes.php');
		$config = HTMLPurifier_Config::createDefault();
		// 保留a标签上的target属性
		$config->set('HTML.TargetBlank', TRUE);
		$obj = new HTMLPurifier($config);  
	}
	return $obj->purify($val);  
}
//获取某栏目下的子集栏目树
function getTree($data,$id=0){
	if(!isset($data['old'])){
		$data=array('new'=>array(),'old'=>$data);
	}
	foreach ($data['old'] as $k => $v){
		if($v['pid']==$id){
			$data['new'][]=$v;
			unset($data['old'][$k]);
			getTree($data,$v['id']);
		}
	}
	return $data['new'];
}

//获取某栏目向上的栏目树
function getParent(&$data,$sonid,$id=0){
	if(!isset($data['odl'])){
		$data=array('new'=>array(),'odl'=>$data,'flag'=>array());
	}
	foreach ($data['odl'] as $k => $v){
		if($v['id']==$sonid){
			$data['new'][]=$v;
			unset($data['odl'][$k]);
			getParent($data,$v['pid']);
		}
	}
	$len=count($data['new']);
	for($i=0,$j=$len-1;$i<$len;$i++,$j--){
	 $data['flag'][$i]=$data['new'][$j];
	}
	return $data['flag'];
}

//下标自然排序
function keysort($data){
	$len=count($data);
	$db=array();
	$n=0;
	foreach($data as $k=>$v){
		$db[$n]=$v;
		$n++;
	}
	//dump($db);die;
	return $db;
}

/*多维数组全组合*/
function array_combination($data,$i=0,$arr,$n=0){
	if($i<count($data)){
		$k1=$i;
		$j=$i+1;
 		foreach($data["$k1"] as $k=>$v){	
 			$arr["$n"][]=$v;//赋值
			$arr=array_combination($data,$j,$arr,$n);//递归
			$a='';//每次遍历清空缓存
			for($m=0;$m<$k1;$m++){//暂时保存他前面数组的值(值的个数为它前面的数组个数)
				$a[]=$arr["$n"]["$m"];
			}
			if(($k1+1)==count($data)){//该数组为最后一个时,每遍历一次,排列组合出现的次数的下标+1
				$n++;
			}else{//不是最后一个,下标的值为:当前所在该数组的下标值+它后面数组长度值的累乘
				$k2=$k1+1;
				$n1=1;
	 			for($k2;$k2<count($data);$k2++){//累乘
					$n1*=count($data["$k2"]);
				} 
				$n+=$n1;
			}
			if($k1!=0){//如果不是第一个(第一个前面木有数组了)则将保存的值赋给新的组合(组合为一维数组)
				if($k<count($data["$k1"])-1){//值保存的次数为该数组长度减1(第一次遍历时,上个数组已经传过来，值已经存在，不需要赋予)
					foreach($a as $v){//循环赋值
						$arr["$n"][]=$v;
					}
				}
			}							
		} 
	}
	return $arr;
}

//字符串截取
function PRCsubstr($str,$length=15,$suffix='...',$encoding='utf-8')
{
	$str_len=mb_strlen($str,$encoding);
	if( $str_len<=$length)
		return $str;
	else
		return mb_substr($str,0,$length,$encoding).$suffix;
}

//概率算法
function get_rand($proArr){ 
    $result = '';    
    //概率数组的总概率精度   
    $proSum = array_sum($proArr);    
    //概率数组循环   
    foreach ($proArr as $key => $proCur) {   
        $randNum = mt_rand(1, $proSum);   
        if ($randNum <= $proCur) {   
            $result = $key;   
            break;   
        } else {   
            $proSum -= $proCur;   
        }         
    }   
    unset ($proArr);    
    return $result;   
}   

//设置随机编码
function setnum($n,$istime='y',$charset = 'abcdefghkmnprstuvwxyzABCDEFGHKMNPRSTUVWXYZ23456789'){
	$_len = strlen($charset)-1;
	for($i=0;$i<$n;$i++) {
		$num .= $charset[mt_rand(0,$_len)];
	}
	if($istime=='y'){
		$num.=time();
	}
	return $num;
}

/*无级分类*/
function tree_1($node,$field='id',$pid=0, $pname="顶级分类", $html="|__", $level=0){
	$array1 = array();
	foreach($node as $v){
		if($v['pid'] == $pid){
			$v['pname'] = $pname;
			if($v['pid'] == 0){
				$v['html'] = "";
			}else{
				$v['html'] = $html.str_repeat("_", $level);
			}
				
			$array1[] = $v;
			// tree($node,$v['id']);
			$array1 = array_merge($array1,tree_1($node,$field,$v["$field"],$v['name'], $html="|__", $level+1));
		}
	}
	return $array1;
}

/**
* 非递归 获取分类树
* @access public
* @param  array $category  所有分类   
* @param  int   $type      传来的数据是否有将分类id作为数组key  
* @return array $cat_ids 执行结果
*/ 
function get_child($category = array()  , $pid = 'pid' , $type = 1){
    $tree = array();  
    if($type == 1){
	    //第一步，将分类id作为数组key,并创建child单元  
		foreach($category as $v){  
		    $tree[$v['id']] = $v;  
		    $tree[$v['id']]['child'] = array();  
		}     	
    }else{
    	$tree = $category;
    } 
	//第二步，利用引用，将每个分类添加到父类child数组中，这样一次遍历即可形成树形结构。  
	foreach($tree as $k => $v){  
	    if($v[$pid] != 0){ 
            $tree[$v[$pid]]['child'][] = &$tree[$k];//注意：此处必须传引用否则结果不对      
	        if(!isset($tree[$k]['child'])){  
	            unset($tree[$k]['child']); //如果child为空，则删除该child元素（可选）  
	        }  
	    }  
	}  	
	//第三步，删除无用的非根节点数据  
	foreach($tree as $k => $v){  
	    if($v[$pid] != 0 || !isset($v[$pid])){ 
	        unset($tree[$k]);  
	    }
	} 
	return $tree;
}


/**
 * 返回经stripslashes处理过的字符串或数组
 * @param $string 需要处理的字符串或数组
 * @return mixed
 */
function new_stripslashes($string) {
	if(!is_array($string)) return stripslashes($string);
	foreach($string as $key => $val) $string[$key] = new_stripslashes($val);
	return $string;
}

/**
 * 返回经addslashes处理过的字符串或数组
 * @param $string 需要处理的字符串或数组
 * @return mixed
 */
function new_addslashes($string){
	if(!is_array($string)) return addslashes($string);
	foreach($string as $key => $val) $string[$key] = new_addslashes($val);
	return $string;
}

/**
 * 将数组转换为字符串
 *
 * @param    array   $data       数组
 * @param    bool    $isformdata 如果为0，则不使用new_stripslashes处理，可选参数，默认为1
 * @return   string  返回字符串，如果，data为空，则返回空
 */
function array2string($data, $isformdata = 1) {
	if($data == '') return '';
	if($isformdata) $data = new_stripslashes($data);
	return addslashes(var_export($data, TRUE));
}

/**
* 将字符串转换为数组
*
* @param	string	$data	字符串
* @return	array	返回数组格式，如果，data为空，则返回空数组
*/
function string2array($data) {
	if($data == '') return array();
	$array='';
	@eval("\$array = $data;");
	return $array;
}

//判断是否为手机用户
function is_mobile() {
	$user_agent = $_SERVER['HTTP_USER_AGENT'];
	$mobile_agents = array("240x320","acer","acoon","acs-","abacho","ahong","airness","alcatel","amoi",
			"android","anywhereyougo.com","applewebkit/525","applewebkit/532","asus","audio",
			"au-mic","avantogo","becker","benq","bilbo","bird","blackberry","blazer","bleu",
			"cdm-","compal","coolpad","danger","dbtel","dopod","elaine","eric","etouch","fly ",
			"fly_","fly-","go.web","goodaccess","gradiente","grundig","haier","hedy","hitachi",
			"htc","huawei","hutchison","inno","ipad","ipaq","iphone","ipod","jbrowser","kddi",
			"kgt","kwc","lenovo","lg ","lg2","lg3","lg4","lg5","lg7","lg8","lg9","lg-","lge-","lge9","longcos","maemo",
			"mercator","meridian","micromax","midp","mini","mitsu","mmm","mmp","mobi","mot-",
			"moto","nec-","netfront","newgen","nexian","nf-browser","nintendo","nitro","nokia",
			"nook","novarra","obigo","palm","panasonic","pantech","philips","phone","pg-",
			"playstation","pocket","pt-","qc-","qtek","rover","sagem","sama","samu","sanyo",
			"samsung","sch-","scooter","sec-","sendo","sgh-","sharp","siemens","sie-","softbank",
			"sony","spice","sprint","spv","symbian","tablet","talkabout","tcl-","teleca","telit",
			"tianyu","tim-","toshiba","tsm","up.browser","utec","utstar","verykool","virgin",
			"vk-","voda","voxtel","vx","wap","wellco","wig browser","wii","windows ce",
			"wireless","xda","xde","zte");
	$is_mobile = false;
	foreach ($mobile_agents as $device) {
		if (stristr($user_agent, $device)) {
			$is_mobile = true;
			break;
		}
	}
	return $is_mobile;
}

//判断是否为手机
function ismobile(){
	if (!is_mobile()) {
		return false;
	}else{
		return true;
	}
}

/**
 *  无限分类列表
 *@param $types array 分类结果集
 *@param $html string 子级分类填充字符串
 *@param $pid int 父类id
 *@param $num int 填充字符串个数
 *@return array 返回排序后结果集
 */
function getList($types, $html = '----', $pid = 0, $num = 0){
    $arr = array();
    foreach($types as $v){
        if($v['pid'] == $pid){
            //$v['num'] = $num + 1;//可做自定义级别使用
            $v['html'] = str_repeat($html, $num);//填充字符串个数
            $arr[] = $v;
            $arr = array_merge($arr, getList($types, $html, $v['id'], $num + 1));//递归把子类压入父类数组后
        }
    }
    return $arr;
}

/*获取目录*/
function getDir($dir){
    $dh  = opendir($dir);
    $arr = array();
    while (($file = readdir($dh)) !== false){
        if($file != '.' && $file != '..'){
            $son_dir    = $dir.'/'.$file;
            if(is_dir($son_dir)){
                $arr[$file] = getDir($son_dir);
            }else{
                $arr[] = $file;
            }           
        }
    }
    return $arr;
}

/*
 * 判断是否为合法的日期格式
 * */
function isDate($dateString , $gs = 'Y-m-d H:i:s') {
    return strtotime( date($gs , strtotime($dateString)) ) === strtotime( $dateString );
}

/**
 *  无限分类级别排序    子类作为父类的数组值的多维数组
 *@param $types array 分类结果集
 *@param $name string 自定义子类下标名称
 *@param $pid int 父类id
 *@return array 返回级别排序后结果集(多维数组)
 */
function getLayer($types, $name = 'child', $pid = 0){
    $arr = array();
    foreach($types as $v){
        if($v['pid'] == $pid){
            $v[$name] = getLayer($types, $name, $v['id']);//递归 把子类作为数组值压入数组中
            $arr[$v['id']] = $v;
        }
    }
    return $arr;
}

function array_column($array , $key , $key_){
    $new_array = array();
    foreach($array as $v){
        if(isset($key_)){
            $new_array[$v[$key_]] = $v[$key];
    }
}else{
    $new_array[] = $v[$key];
}
return $new_array;
}

function array_all_column($array , $key){
    $new_array = array();
    foreach($array as $v){
        if($key){
            $new_array[$v[$key]] = $v;
        }     
    }
    return $new_array;
}

function pageHtml($url_ , $data , $parameter = ''){
        $wenhao = strpos($url_ , '?') ? '&' : '?';
	    $html = array("<div class='turn-page'>");
	    if($data['prev']){  
	        $url = $parameter ? $url_."{$wenhao}p={$data['prev']}&$parameter": $url_."{$wenhao}p={$data['prev']}";
	        array_push($html,"<a href='".$url."'><div class='left-btn'><<上一页</div></a>");
	    }else{
	        array_push($html,"<div class='left-btn'><<上一页</div>");
	    }
	    foreach($data['page'] as $k => $v){
	        if($v == '.'){
	            array_push($html,"<div class='omit'>...</div>");
	        }else
	        if($v != $data['nowPage']){
	            $url = $parameter ? $url_."{$wenhao}p={$v}&$parameter": $url_."{$wenhao}p={$v}";
	            array_push($html,"<a href='".$url."'><div class='page-btn'>{$v}</div></a>");
	        }else{
	            array_push($html,"<div class='page-btn active'>{$v}</div>");
	        }
	    }
	    if($data['next']){
	        $url = $parameter ? $url_."{$wenhao}p={$data['next']}&$parameter": $url_."{$wenhao}p={$data['next']}";
	        array_push($html,"<a href='".$url."'><div class='right-btn'>下一页>></div></a>");
	    }else{
	        array_push($html,"<div class='right-btn'>下一页>></div>");
	    }
	
	    array_push($html,"</div>");
	    array_push($html,"<p class='to-page'>到第   ");
	    array_push($html,"<input class='go_number' type='text' value='1'>页<input type='button' value='确定' class='go'>");
	    array_push($html,"</p>");
	    $html = implode('',$html);
	    return $html;
}

/*获取访问用户IP*/
	function getIP()
{
    static $realip;
    if (isset($_SERVER)){
        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
            $realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
            $realip = $_SERVER["HTTP_CLIENT_IP"];
        } else {
            $realip = $_SERVER["REMOTE_ADDR"];
        }
    } else {
        if (getenv("HTTP_X_FORWARDED_FOR")){
            $realip = getenv("HTTP_X_FORWARDED_FOR");
        } else if (getenv("HTTP_CLIENT_IP")) {
            $realip = getenv("HTTP_CLIENT_IP");
        } else {
            $realip = getenv("REMOTE_ADDR");
        } 
    }
    return $realip;
}

function getpage($count, $pagesize = 10) {
    $p = new Think\Page($count, $pagesize);
    $p->setConfig('header', '<li class="rows">共<b>%TOTAL_ROW%</b>条记录&nbsp;第<b>%NOW_PAGE%</b>页/共<b>%TOTAL_PAGE%</b>页</li>');
    $p->setConfig('prev', '上一页');
    $p->setConfig('next', '下一页');
    $p->setConfig('last', '末页');
    $p->setConfig('first', '首页');
    $p->setConfig('theme', '%FIRST%%UP_PAGE%%LINK_PAGE%%DOWN_PAGE%%END%%HEADER%');
    $p->lastSuffix = false;//最后一页不显示为总页数
    return $p;
}	