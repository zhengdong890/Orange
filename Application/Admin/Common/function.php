<?php
//递归重组节点信息为多维数组
function node_merge($node, $access= null, $pid = 0) {
	$arr = array();
	foreach($node as $v){
		if(is_array($access)){
			$v['access'] = in_array($v['id'], $access) ? 1: 0;
		}
		if($v['pid'] == $pid){
			$v['child'] = node_merge($node, $access, $v['id']);
			$arr[] = $v;
		}
	}
  return $arr;
}

//无限极分类一维数组
function tree($node, $pid=0){
	$array1 = array();
	foreach($node as $v){
		if($v['pid'] == $pid){
			$array1[] = $v['id'];
			tree($node,$v['id']);
		}
	}
	return $array1;
}


function delNodes($nodeId){
	$nodes = M('node')->select();
	$ids = tree($nodes,$nodeId);
	$ids[] = $nodeId;
	return $ids;
}

?>