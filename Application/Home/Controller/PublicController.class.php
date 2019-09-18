<?php
namespace Home\Controller;
use Think\Controller;
header("content-type:text/html;charset=utf-8");
class PublicController extends Controller {	
    public function sitemap(){
        C('TMPL_TEMPLATE_SUFFIX','.xml');
        $this->display('sitemap', 'utf-8', 'text/xml');
  
    }
	
}
