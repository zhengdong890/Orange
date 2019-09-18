<?php
namespace Think;
header("content-type:text/html;charset=utf-8");
//验证码类  
class ValidateCode {  
    private $charset = 'abcdefghkmnprstuvwxyzABCDEFGHKMNPRSTUVWXYZ23456789';//随机因子  
    private $code;                            //验证码  
    private $codelen = 4;                    //验证码长度  
    private $width = 130;                    //宽度  
    private $height = 50;                    //高度  
    private $img;                                //图形资源句柄  
    private $font;                                //指定的字体  
    private $fontsize = 20;                //指定字体大小  
    private $fontcolor;                        //指定字体颜色  
    private $critical=156;//随机设置背景和前景临界值
  
    //构造方法初始化  
    public function __construct() {  
         $this->font = 'Public/font/elephant.ttf';  
         $this->critical = mt_rand(50,156);
    }  
    
    //生成随机码  
    private function createCode() {  
        $_len = strlen($this->charset)-1;  
        for ($i=0;$i<$this->codelen;$i++) {  
            $this->code .= $this->charset[mt_rand(0,$_len)];  
        }         
    }  
  
  
    //生成背景  
    private function createBg() {
    	/*设置背景颜色*/
    	$critical=$this->critical;
    	$r=mt_rand($critical,125);
    	$g=mt_rand($critical,125);
    	$b=mt_rand($critical,125);
        $this->img = imagecreatetruecolor($this->width, $this->height);  
        $bgcolor=$this->bgcolor;
        $color = imagecolorallocate($this->img,mt_rand($critical,255),mt_rand($critical,255),mt_rand($critical,255));  
        imagefilledrectangle($this->img,0,$this->height,$this->width,0,$color);  
    }  
  
  
    //生成文字  
    private function createFont() {
    	/*设置字体颜色*/
    	$critical=$this->critical;
    	$critical++;
        $_x = $this->width / $this->codelen-7; 
        $fontcolor=$this->fontcolor;
        for ($i=0;$i<$this->codelen;$i++) {  
            $this->fontcolor = imagecolorallocate($this->img,mt_rand(0,$critical),mt_rand(0,$critical),mt_rand(0,$critical));  
            imagettftext($this->img,$this->fontsize,mt_rand(0,30),$_x*$i+mt_rand(1,5),$this->height / 1.4,$this->fontcolor,$this->font,$this->code[$i]);  
        }  
    }  
  
  
    //生成线条、雪花  
    private function createLine(){  
    	$critical=$this->critical;
    	$critical++;
    	$fontcolor=$this->fontcolor;
        for ($i=0;$i<6;$i++) {  
            $color = imagecolorallocate($this->img,mt_rand(0,$critical),mt_rand(0,$critical),mt_rand(0,$critical));  
            imageline($this->img,mt_rand(0,$this->width),mt_rand(0,$this->height),mt_rand(0,$this->width),mt_rand(0,$this->height),$color);  
        }  
        for ($i=0;$i<50;$i++) {  
            $color = imagecolorallocate($this->img,mt_rand(0,$critical),mt_rand(0,$critical),mt_rand(0,$critical));  
            imagestring($this->img,mt_rand(1,5),mt_rand(0,$this->width),mt_rand(0,$this->height),'*',$color);  
        } 
    }  
  
  
    //输出  
    private function outPut() {  
        header('Content-type:image/png');  
        ob_clean();
        imagepng($this->img);  
        imagedestroy($this->img);  
    }  
  
  
    //对外生成  
    public function doimg() { 
        $this->createBg();  
        $this->createCode();  
       // $this->createLine();  
        $this->createFont();  
        $this->outPut();  
    }  
  
  
    //获取验证码  
    public function getCode() {  
        return strtolower($this->code);  
    }  
}