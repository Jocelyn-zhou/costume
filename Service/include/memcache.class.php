<?php
class MemCaches{
    //声明静态成员变量
    private  $mem = null;
    //private  $cache = null;
    
     function __construct() {
        $this->mem = new Memcache();
        //$this->mem->connect('118.178.182.224','18392'); //写入缓存地址,端口
//        $this->mem->addServer('118.178.182.224','18392');
         $this->mem->addServer('localhost','3306');
    }
    // function  __destruct() {
    //      echo '__destruct</br>';
	// 	 $this->mem->close();
	// }
    //为当前类创建对象
    // private static function Men(){
    //     echo 'Men</br>';
    //     $this->cache = new MCACHE();
    //     return $this->mem;
    // }
    /*
     * 添加缓存数据
     * @param string $key 获取数据唯一key
     * @param String||Array $value 缓存数据
     * @param $time memcache生存周期(秒)
     */
    public function Set($key,$value,$time=0){
        //$this->mem->set($key,$value,0,$time);
        return $this->mem->set($this->SetName($key),$value,MEMCACHE_COMPRESSED,$time);
    }
    /*
     * 添加缓存数据
     * @param string $key 获取数据唯一key
     * @param String||Array $value 缓存数据
     * @param $time memcache生存周期(秒)
     */
    public function Add($key,$value,$time=0){
        return $this->mem->add($this->SetName($key),$value,MEMCACHE_COMPRESSED,$time);
    }
    /*
     * 获取缓存数据
     * @param string $key
     * @return
     */
    public function Get($key){
        return $this->mem->get($this->SetName($key));
    }
    /*
     * 删除对应缓存数据
     * @param string $key
     * @return
     */
    public function Del($key){
        return $this->mem->delete($this->SetName($key));
    }
    /*
     * 删除所有缓存数据
     */
    public function DelAll(){
        return $this->mem->flush();
    }
    /*
     * 缓存数据状态
     */
    public function Status(){
        return $this->mem->getStats();
    }
    private function SetName($key){
        return md5(strtolower($key));
    }
}
// $memc=new MemCaches();
// echo'memcache:</br>';
// //$memc->Set('numers1','hello memcache!'.time(),300);
// print_r($memc->Status());
// echo '</br>';
// var_dump($memc->Get('numers1'));
