<?php
namespace Com;
header("content-type:text/html;charset=utf-8");
class My_Page{
	//分页
	public function pages($data,$pagesize=5,$url='',$get_where){
		$total=count($data);//总记录数
		$totalpage=ceil($total/$pagesize);//总页数
		$p=!empty($_GET['p'])?$_GET['p']:1;
		$pageshow='';//分页的样子
		if(!empty($url)){
			$url.='&';
		}
		if($p>1){
			$prev=$p-1;
			$pageshow.="<a href='?{$url}p=1{$get_where}'>首页</a><a href='?{$url}p={$prev}{$get_where}'>上一页</a>";
		}	
		for($i=2;$i>=1;$i--){
			$tmp=$p-$i;
			if($tmp<1) continue;
			$pageshow.=" <a href='?{$url}p={$tmp}{$get_where}'>{$tmp}</a>";	
		}	
		$pageshow.="<span> {$p} </span>";
		for($i=1;$i<=2;$i++){
		    $tmp=$p+$i;
		    if($tmp>$totalpage) break;
		    $pageshow.=" <a href='?{$url}p={$tmp}{$get_where}'>{$tmp}</a>";	
		}	
		if($p<$totalpage){
		    $next=$p+1;
		    $pageshow.=" <a href='?{$url}p={$totalpage}{$get_where}'>尾页</a><a href='?{$url}p={$next}{$get_where}'>下一页</a>";
		}
		$pageshow.="<p>{$p}/{$totalpage} 共{$totalpage}页{$total}条数据</p>";
		for($i=($p-1)*$pagesize,$j=0;$j<=$pagesize-1;$i++,$j++){
		    if($data["$i"]){
		        $list["$j"]=$data["$i"];
	        }
	    }
	    $res['list']=$list;
	    $res['page']=$pageshow;
	    return $res;
    } 
}