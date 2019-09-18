<?php
namespace Think\Session\Driver;
header("content-type:text/html;charset=utf-8");
class Redis {
  private $redis; // redis实例
  private $prefix = 'sess_'; // session_id前缀
   
  // 会话开始时，会执行该方法，连接redis服务器
  public function open($path, $name) {
      $this->redis = new \Redis();
      return $this->redis->connect('127.0.0.1', 6379);
  }
   
  // 会话结束时，调用该方法，关闭redis连接
  public function close() {
      $this->redis->close();
      return true;
  }
   
  // 会话保存数据时调用该方法，在脚本执行完或session_write_close方法调用之后调用
  public function write($session_id, $data) {
      return $this->redis->hMSet($this->prefix.$session_id, array('expires' => time(), 'data' => $data));
  }
   
  // 在自动开始会话或者通过调用 session_start() 函数手动开始会话之后，PHP 内部调用 read 回调函数来获取会话数据。
  public function read($session_id) {
      if($this->redis->exists($this->prefix.$session_id)) {
          return $this->redis->hGet($this->prefix.$session_id, 'data');
      }
      return '';
  }
   
  // 清除会话中的数据，当调用session_destroy()函数，或者调用 session_regenerate_id()函数并且设置 destroy 参数为 TRUE 时,会调用此回调函数。
  public function destroy($session_id) {
      if($this->redis->exists($this->prefix.$session_id)) {
          return $this->redis->del($this->prefix.$session_id) > 0 ? true : false;
      }
      return true;
  }
   
  // 垃圾回收函数，调用周期由 session.gc_probability 和 session.gc_divisor 参数控制
  public function gc($maxlifetime) {
      $allKeys = $this->redis->keys("{$this->prefix}*");
      foreach($allKeys as $key) {
          if($this->redis->exists($key) && $this->redis->hGet($key, 'expires') + $maxlifetime < time()) {
              $this->redis->del($key);
          }
      }
      return true;
  }
}
