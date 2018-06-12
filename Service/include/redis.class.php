<?php

/**
 * Redis 操作，支持 Master/Slave 的负载集群
 *
 * @author jackluo
 */
class RedisClass{
    // 是否使用 M/S 的读写集群方案
    private $_isUseCluster = false;
    // Slave 句柄标记
    private $_sn = 0;
    // 服务器连接句柄
    private $_linkHandle = array(
      'master'=>null,// 只支持一台 Master
      'slave'=>array(),// 可以有多台 Slave
    );
    /**
     * 构造函数
     *
     * @param boolean $isUseCluster 是否采用 M/S 方案
     */
    public function __construct($isUseCluster=false){
      $this->_isUseCluster = $isUseCluster;
      $this->connect();
    }
    /**
     * 连接服务器,注意：这里使用长连接，提高效率，但不会自动关闭
     *
     * @param array $config Redis服务器配置
     * @param boolean $isMaster 当前添加的服务器是否为 Master 服务器
     * @return boolean
     */
    public function connect($config=array('host'=>'101.37.13.90','port'=>18393), $isMaster=true){
      // 设置 Master 连接
      if($isMaster){
        $this->_linkHandle['master'] = new Redis();
        $ret = $this->_linkHandle['master']->pconnect($config['host'],$config['port']);
      }else{
        // 多个 Slave 连接
        $this->_linkHandle['slave'][$this->_sn] = new Redis();
        $ret = $this->_linkHandle['slave'][$this->_sn]->pconnect($config['host'],$config['port']);
        ++$this->_sn;
      }
      return $ret;
    }
    /**
     * 关闭连接
     *
     * @param int $flag 关闭选择 0:关闭 Master 1:关闭 Slave 2:关闭所有
     * @return boolean
     */
    public function close($flag=2){
      switch($flag){
        // 关闭 Master
        case 0:
          $this->getRedis()->close();
        break;
        // 关闭 Slave
        case 1:
          for($i=0; $i<$this->_sn; ++$i){
            $this->_linkHandle['slave'][$i]->close();
          }
        break;
        // 关闭所有
        case 2:
          $this->getRedis()->close();
          for($i=0; $i<$this->_sn; ++$i){
            $this->_linkHandle['slave'][$i]->close();
          }
        break;
      }
      return true;
    }
    /**
     * 得到 Redis 原始对象可以有更多的操作
     *
     * @param boolean $isMaster 返回服务器的类型 true:返回Master false:返回Slave
     * @param boolean $slaveOne 返回的Slave选择 true:负载均衡随机返回一个Slave选择 false:返回所有的Slave选择
     * @return redis object
     */
    public function getRedis($isMaster=true,$slaveOne=true){
      // 只返回 Master
      if($isMaster){
        return $this->_linkHandle['master'];
      }else{
        return $slaveOne ? $this->_getSlaveRedis() : $this->_linkHandle['slave'];
      }
    }
    
  /*
    // magic function 
    public function __call($name,$arguments){
      return call_user_func($name,$arguments);  
    }
  */
     /**
     * 随机 HASH 得到 Redis Slave 服务器句柄
     *
     * @return redis object
     */
    private function _getSlaveRedis(){
        // 就一台 Slave 机直接返回
        if($this->_sn <= 1){
            return $this->_linkHandle['slave'][0];
        }
        // 随机 Hash 得到 Slave 的句柄
        $hash = $this->_hashId(mt_rand(), $this->_sn);
        return $this->_linkHandle['slave'][$hash];
    }
    /**
     * 根据ID得到 hash 后 0～m-1 之间的值
     *
     * @param string $id
     * @param int $m
     * @return int
     */
    private function _hashId($id,$m=10)
    {
        //把字符串K转换为 0～m-1 之间的一个值作为对应记录的散列地址
        $k = md5($id);
        $l = strlen($k);
        $b = bin2hex($k);
        $h = 0;
        for($i=0;$i<$l;$i++)
        {
            //相加模式HASH
            $h += substr($b,$i*2,2);
        }
        $hash = ($h*1)%$m;
        return $hash;
    }
     /**
     * 添空当前数据库
     *
     * @return boolean
     */
    // public function Clear(){
    //     return $this->getRedis()->flushDB();
    // }
    /**
     * 删除缓存
     *
     * @param string || array $key 缓存KEY，支持单个健:"key1" 或多个健:array('key1','key2')
     * @return int 删除的健的数量
     */
    public function Del($key){
        // $key => "key1" || array('key1','key2')
        return $this->getRedis()->del($key);
    }
    /**
     * @param string $key
     * @return 0|1
     */
    public function Exists($key){
        return $this->getRedis()->exists($key);
    }
    /* =================== String =================== */
   /**
     * 写缓存
     *
     * @param string $key 组存KEY
     * @param string $value 缓存值
     * @param int $expire 过期时间， 0:表示无过期时间
     */
    public function StringSet($key, $value, $expire=0){
        // 永不超时
        if($expire == 0){
        $ret = $this->getRedis()->set($key, $value);
        }else{
        $ret = $this->getRedis()->setex($key, $expire, $value);
        }
        return $ret;
    }
    /**
     * 读缓存
     *
     * @param string $key 缓存KEY,支持一次取多个 $key = array('key1','key2')
     * @return string || boolean 失败返回 false, 成功返回字符串
     */
    public function StringGet($key){
        // 是否一次取多个值
        $func = is_array($key) ? 'mGet' : 'get';
        // 没有使用M/S
        if(! $this->_isUseCluster){
        return $this->getRedis()->{$func}($key);
        }
        // 使用了 M/S
        return $this->_getSlaveRedis()->{$func}($key);
    }
    /**
     * 值加加操作,类似 ++$i ,如果 key 不存在时自动设置为 0 后进行加加操作
     *
     * @param string $key 缓存KEY
     * @param int $default 操作时的默认值
     * @return int　操作后的值
     */
    public function StringIncr($key,$default=1){
        if($default == 1){
            return $this->getRedis()->incr($key);
        }else{
            return $this->getRedis()->incrBy($key, $default);
        }
    }
    /**
     * 值减减操作,类似 --$i ,如果 key 不存在时自动设置为 0 后进行减减操作
     *
     * @param string $key 缓存KEY
     * @param int $default 操作时的默认值
     * @return int　操作后的值
     */
    public function StringDecr($key,$default=1){
        if($default == 1){
            return $this->getRedis()->decr($key);
        }else{
            return $this->getRedis()->decrBy($key, $default);
        }
    }
    /**
     * 条件形式设置缓存，如果 key 不存时就设置，存在时设置失败
     *
     * @param string $key 缓存KEY
     * @param string $value 缓存值
     * @return boolean
     */
    public function StringSetnx($key, $value){
        return $this->getRedis()->setnx($key, $value);
    }
    /**
     * 获取当前key的长度
     *
     * @param string $key 缓存KEY
     * @return int 长度
     */
    public function StringLength($key){
        return $this->getRedis()->strlen($key);
    }
    
    /* =================== List =================== */
    /**
     * 获取当前key的长度
     *
     * @param string $key 缓存KEY
     * @return int 长度
     */
    public function ListLength($key){
        return $this->getRedis()->llen($key);
    }
    /**
     * 在名称为key的list尾添加一个值为value的元素  
     *
     * @param string $key 缓存KEY
     * @return int 长度
     */
    public function ListRPush($key,$value,$isarray=false){
        $num=0;
        if($isarray){
            foreach($value as $k=>$v){
                $num= $this->getRedis()->rpush($key,json_encode($v));
            }
        }else
            $num= $this->getRedis()->rpush($key,json_encode($value));
        return $num;
    }
    /**
     * 在名称为key的list头添加一个值为value的元素  
     *
     * @param string $key 缓存KEY
     * @return int 长度
     */
    public function ListLPush($key,$value,$isarray=false){
        $num=0;
        if($isarray){
            $value=array_reverse($value);
            foreach($value as $k=>$v){
                $num= $this->getRedis()->lpush($key,json_encode($v));
            }
        }else
            $num= $this->getRedis()->lpush($key,json_encode($value));
        return $num;
    }
    /**
     * 返回队列中指定索引的元素
     * @param unknown $key
     * @param unknown $index
     */
    public function ListIndex($key,$index)
    {
        return json_decode($this->getRedis()->lindex($key,$index));
    }
    /**
     * 返回队列指定区间的元素
     * @param unknown $key
     * @param unknown $start
     * @param unknown $end
     */
    public function ListRange($key,$start=0,$end=-1)
    {
        $list=$this->getRedis()->lrange($key,$start,$end);
        if(!empty($list)){
            foreach($list as $key=>$value){
                $list[$key]=json_decode($value);
            }
        }
        return $list;
    }
    /**
     * 设定队列中指定index的值。
     * @param unknown $key
     * @param unknown $index
     * @param unknown $value
     */
    public function ListSet($key,$index,$value)
    {
        return $this->getRedis()->lset($key,$index,$value);
    }
    /**
     * 删除值为vaule的count个元素
     * PHP-REDIS扩展的数据顺序与命令的顺序不太一样，不知道是不是bug
     * count>0 从尾部开始
     *  >0　从头部开始
     *  =0　删除全部
     * @param unknown $key
     * @param unknown $count
     * @param unknown $value
     */
    public function ListRem($key,$count,$value)
    {
        return $this->getRedis()->lrem($key,$value,$count);
    }
     
    /**
     * 删除并返回队列中的头元素。
     * @param unknown $key
     */
    public function ListLPop($key)
    {
        return json_decode($this->getRedis()->lpop($key));
    }
     
    /**
     * 删除并返回队列中的尾元素
     * @param unknown $key
     */
    public function ListRPop($key)
    {
        return json_decode($this->getRedis()->rpop($key));
    }

    /* =================== Hash =================== */
    /**
     *  如果哈希表不存在，一个新的哈希表被创建并进行 HSET 操作。 如果字段已经存在于哈希表中，旧值将被覆盖。
     */
    public function HashSet($name,$key,$value){
        return $this->getRedis()->hset($name,$key,json_encode($value));
    }
    /**
     *  get hash opeation
     */
    public function HashGet($name,$key = null){
        if($key){
            return json_decode($this->getRedis()->hget($name,$key));
        }else{
         $rows= $this->getRedis()->hgetall($name);
         if($rows){
            foreach($rows as $k=>$v){
                $rows[$k] = json_decode($v);
            }
         }
         return $rows;
        }
    }
    /**
     * 判断hash表中，指定field是不是存在
     * @param string $key 缓存key
     * @param string  $field 字段
     * @return bool
     */
    public function HashExists($key,$field)
    {
        return $this->getRedis()->hexists($key,$field);
    }
     
    /**
     * 删除hash表中指定字段 ,支持批量删除
     * @param string $key 缓存key
     * @param string  $field 字段 string/array
     * @return int
     */
    public function HashDel($key,$field)
    {
        $delNum=0;
        if(is_array($field)){
            foreach($field as $key=>$value)
            {
                $delNum+=$this->getRedis()->hdel($key,$value);
            }
        }else
            $delNum+=$this->getRedis()->hdel($key,$field);
        return $delNum;
    }
    /**
     * 返回hash表元素个数
     * @param string $key 缓存key
     * @return int|bool
     */
    public function HashLength($key)
    {
        return $this->getRedis()->hlen($key);
    }
    /**
     * 为hash表设定一个字段的值,如果字段存在，返回false
     * @param string $key 缓存key
     * @param string  $field 字段
     * @param string $value 值。
     * @return bool
     */
    public function HashSetNx($key,$field,$value)
    {
        return $this->getRedis()->hsetnx($key,$field,json_encode($value));
    }
     
    /**
     * 为hash表多个字段设定值。
     * @param string $key
     * @param array $value
     * @return array|bool
     */
    public function HashMSet($key,$value)
    {
        if(!is_array($value))
            return false;
        foreach($value as $k=>$v){
            $value[$k]=json_encode($v);
        }
        return $this->getRedis()->hmset($key,$value); 
    }
     
    /**
     * 为hash表多个字段设定值。
     * @param string $key
     * @param array|string $value string以','号分隔字段
     * @return array|bool
     */
    public function HashMGet($key,$field)
    {
        if(!is_array($field))
            $field=explode(',', $field);
        $rows= $this->getRedis()->hmget($key,$field);
        if($rows){
            foreach($rows as $k=>$v){
                $rows[$k] = json_decode($v);
            }
         }
        return $rows;
    }
    /**
     * 为hash表设这累加，可以负数
     * @param string $key
     * @param int $field
     * @param string $value
     * @return bool
     */
    public function HashIncrBy($key,$field,$value)
    {
        $value=intval($value);
        return $this->getRedis()->hincrby($key,$field,$value);
    }
    /**
     * 返回所有hash表的所有字段 名称
     * @param string $key
     * @return array|bool
     */
    public function HashKeys($key)
    {
        return $this->getRedis()->hkeys($key);
    }
    /**
     * 返回所有hash表的字段值，为一个索引数组
     * @param string $key
     * @return array|bool
     */
    public function HashVals($key)
    {
        $rows = $this->getRedis()->hvals($key);
        if($rows){
            foreach($rows as $k=>$v){
                $rows[$k] = json_decode($v);
            }
         }
         return $rows;
    }
  }// End Class
//Demo
//   include_once('../include/redis.class.php');
  
//   $redis = new RedisClass();
//   $redis->connect(array('host'=>'101.37.13.90','port'=>18393));
// $array=array('id'=>999,'name'=>'张三');
// $array1=array(array('id'=>999,'name'=>'王五'),array('id'=>998,'name'=>'李四'));
// echo '<br>';
// var_dump($redis -> HashSet('s2','filed1',$array));
// echo '<br>';
// var_dump($redis -> HashSet('s2','filed2',$array1));
// echo '<br>';
// var_dump($redis -> HashGet('s2'));
// var_dump($redis -> ListLPush('s1',$array));     // key 不存在，创建一个新的列表, 返回 int 1
// echo '<br>';
// var_dump($redis -> ListLPush('s1',$array1,true));     // key 不存在，创建一个新的列表, 返回 int 1
// echo '<br>';
// var_dump($redis -> ListLPush('s1',time().'姓名时间'));     // key 不存在，创建一个新的列表, 返回 int 1


//   String——字符串
//   Hash——字典
//   List——列表
//   Set——集合
//   Sorted Set——有序集合

// /*1.Connection*/
//   $redis = new Redis();
//   $redis->connect('127.0.0.1',6379,1);//短链接，本地host，端口为6379，超过1秒放弃链接
//   $redis->open('127.0.0.1',6379,1);//短链接(同上)
//   $redis->pconnect('127.0.0.1',6379,1);//长链接，本地host，端口为6379，超过1秒放弃链接
//   $redis->popen('127.0.0.1',6379,1);//长链接(同上)
//   $redis->auth('password');//登录验证密码，返回【true | false】
//   $redis->select(0);//选择redis库,0~15 共16个库
//   $redis->close();//释放资源
//   $redis->ping(); //检查是否还再链接,[+pong]
//   $redis->ttl('key');//查看失效时间[-1 | timestamps]
//   $redis->persist('key');//移除失效时间[ 1 | 0]
//   $redis->sort('key',[$array]);//返回或保存给定列表、集合、有序集合key中经过排序的元素，$array为参数limit等！【配合$array很强大】 [array|false]
// /*2.共性的运算归类*/
//   $redis->expire('key',10);//设置失效时间[true | false]
//   $redis->move('key',15);//把当前库中的key移动到15库中[0|1]
// //string
//   $redis->strlen('key');//获取当前key的长度
//   $redis->append('key','string');//把string追加到key现有的value中[追加后的个数]
//   $redis->incr('key');//自增1，如不存在key,赋值为1(只对整数有效,存储以10进制64位，redis中为str)[new_num | false]
//   $redis->incrby('key',$num);//自增$num,不存在为赋值,值需为整数[new_num | false]
//   $redis->decr('key');//自减1，[new_num | false]
//   $redis->decrby('key',$num);//自减$num，[ new_num | false]
//   $redis->setex('key',10,'value');//key=value，有效期为10秒[true]
// //list
//   $redis->llen('key');//返回列表key的长度,不存在key返回0， [ len | 0]
// //set
//   $redis->scard('key');//返回集合key的基数(集合中元素的数量)。[num | 0]
//   $redis->sMove('key1', 'key2', 'member');//移动，将member元素从key1集合移动到key2集合。[1 | 0]
// //Zset
//   $redis->zcard('key');//返回集合key的基数(集合中元素的数量)。[num | 0]
//   $redis->zcount('key',0,-1);//返回有序集key中，score值在min和max之间(默认包括score值等于min或max)的成员。[num | 0]
// //hash
//   $redis->hexists('key','field');//查看hash中是否存在field,[1 | 0]
//   $redis->hincrby('key','field',$int_num);//为哈希表key中的域field的值加上量(+|-)num,[new_num | false]
//   $redis->hlen('key');//返回哈希表key中域的数量。[ num | 0]
// /*3.Server*/
//   $redis->dbSize();//返回当前库中的key的个数
//   $redis->flushAll();//清空整个redis[总true]
//   $redis->flushDB();//清空当前redis库[总true]
//   $redis->save();//同步??把数据存储到磁盘-dump.rdb[true]
//   $redis->bgsave();//异步？？把数据存储到磁盘-dump.rdb[true]
//   $redis->info();//查询当前redis的状态 [verson:2.4.5....]
//   $redis->lastSave();//上次存储时间key的时间[timestamp]
//   $redis->watch('key','keyn');//监视一个(或多个) key ，如果在事务执行之前这个(或这些) key 被其他命令所改动，那么事务将被打断 [true]
//   $redis->unwatch('key','keyn');//取消监视一个(或多个) key [true]
//   $redis->multi(Redis::MULTI);//开启事务，事务块内的多条命令会按照先后顺序被放进一个队列当中，最后由 EXEC 命令在一个原子时间内执行。
//   $redis->multi(Redis::PIPELINE);//开启管道，事务块内的多条命令会按照先后顺序被放进一个队列当中，最后由 EXEC 命令在一个原子时间内执行。
//   $redis->exec();//执行所有事务块内的命令，；【事务块内所有命令的返回值，按命令执行的先后顺序排列，当操作被打断时，返回空值 false】
// /*4.String，键值对，创建更新同操作*/
//   $redis->setOption(Redis::OPT_PREFIX,'hf_');//设置表前缀为hf_
//   $redis->set('key',1);//设置key=aa value=1 [true]
//   $redis->mset($arr);//设置一个或多个键值[true]
//   $redis->setnx('key','value');//key=value,key存在返回false[|true]
//   $redis->get('key');//获取key [value]
//   $redis->mget($arr);//(string|arr),返回所查询键的值
//   $redis->del($key_arr);//(string|arr)删除key，支持数组批量删除【返回删除个数】
//   $redis->delete($key_str,$key2,$key3);//删除keys,[del_num]
//   $redis->getset('old_key','new_value');//先获得key的值，然后重新赋值,[old_value | false]
// /*5.List栈的结构,注意表头表尾,创建更新分开操作*/
//   $redis->lpush('key','value');//增，只能将一个值value插入到列表key的表头，不存在就创建 [列表的长度 |false]
//   $redis->rpush('key','value');//增，只能将一个值value插入到列表key的表尾 [列表的长度 |false]
//   $redis->lInsert('key', Redis::AFTER, 'value', 'new_value');//增，将值value插入到列表key当中，位于值value之前或之后。[new_len | false]
//   $redis->lpushx('key','value');//增，只能将一个值value插入到列表key的表头，不存在不创建 [列表的长度 |false]
//   $redis->rpushx('key','value');//增，只能将一个值value插入到列表key的表尾，不存在不创建 [列表的长度 |false]
//   $redis->lpop('key');//删，移除并返回列表key的头元素,[被删元素 | false]
//   $redis->rpop('key');//删，移除并返回列表key的尾元素,[被删元素 | false]
//   $redis->lrem('key','value',0);//删，根据参数count的值，移除列表中与参数value相等的元素count=(0|-n表头向尾|+n表尾向头移除n个value) [被移除的数量 | 0]
//   $redis->ltrim('key',start,end);//删，列表修剪，保留(start,end)之间的值 [true|false]
//   $redis->lset('key',index,'new_v');//改，从表头数，将列表key下标为第index的元素的值为new_v, [true | false]
//   $redis->lindex('key',index);//查，返回列表key中，下标为index的元素[value|false]
//   $redis->lrange('key',0,-1);//查，(start,stop|0,-1)返回列表key中指定区间内的元素，区间以偏移量start和stop指定。[array|false]
// /*6.Set，没有重复的member，创建更新同操作*/
//   $redis->sadd('key','value1','value2','valuen');//增，改，将一个或多个member元素加入到集合key当中，已经存在于集合的member元素将被忽略。[insert_num]
//   $redis->srem('key','value1','value2','valuen');//删，移除集合key中的一个或多个member元素，不存在的member元素会被忽略 [del_num | false]
//   $redis->smembers('key');//查，返回集合key中的所有成员 [array | '']
//   $redis->sismember('key','member');//判断member元素是否是集合key的成员 [1 | 0]
//   $redis->spop('key');//删，移除并返回集合中的一个随机元素 [member | false]
//   $redis->srandmember('key');//查，返回集合中的一个随机元素 [member | false]
//   $redis->sinter('key1','key2','keyn');//查，返回所有给定集合的交集 [array | false]
//   $redis->sunion('key1','key2','keyn');//查，返回所有给定集合的并集 [array | false]
//   $redis->sdiff('key1','key2','keyn');//查，返回所有给定集合的差集 [array | false]
// /*7.Zset，没有重复的member，有排序顺序,创建更新同操作*/
//   $redis->zAdd('key',$score1,$member1,$scoreN,$memberN);//增，改，将一个或多个member元素及其score值加入到有序集key当中。[num | 0]
//   $redis->zrem('key','member1','membern');//删，移除有序集key中的一个或多个成员，不存在的成员将被忽略。[del_num | 0]
//   $redis->zscore('key','member');//查,通过值反拿权 [num | null]
//   $redis->zrange('key',$start,$stop);//查，通过(score从小到大)【排序名次范围】拿member值，返回有序集key中，【指定区间内】的成员 [array | null]
//   $redis->zrevrange('key',$start,$stop);//查，通过(score从大到小)【排序名次范围】拿member值，返回有序集key中，【指定区间内】的成员 [array | null]
//   $redis->zrangebyscore('key',$min,$max[,$config]);//查，通过scroe权范围拿member值，返回有序集key中，指定区间内的(从小到大排)成员[array | null]
//   $redis->zrevrangebyscore('key',$max,$min[,$config]);//查，通过scroe权范围拿member值，返回有序集key中，指定区间内的(从大到小排)成员[array | null]
//   $redis->zrank('key','member');//查，通过member值查(score从小到大)排名结果中的【member排序名次】[order | null]
//   $redis->zrevrank('key','member');//查，通过member值查(score从大到小)排名结果中的【member排序名次】[order | null]
//   $redis->ZINTERSTORE();//交集
//   $redis->ZUNIONSTORE();//差集
// /*8.Hash，表结构，创建更新同操作*/
//   $redis->hset('key','field','value');//增，改，将哈希表key中的域field的值设为value,不存在创建,存在就覆盖【1 | 0】
//   $redis->hget('key','field');//查，取值【value|false】
//   $arr = array('one'=>1,2,3);$arr2 = array('one',0,1);
//   $redis->hmset('key',$arr);//增，改，设置多值$arr为(索引|关联)数组,$arr[key]=field, [ true ]
//   $redis->hmget('key',$arr2);//查，获取指定下标的field，[$arr | false]
//   $redis->hgetall('key');//查，返回哈希表key中的所有域和值。[当key不存在时，返回一个空表]
//   $redis->hkeys('key');//查，返回哈希表key中的所有域。[当key不存在时，返回一个空表]
//   $redis->hvals('key');//查，返回哈希表key中的所有值。[当key不存在时，返回一个空表]
//   $redis->hdel('key',$arr2);//删，删除指定下标的field,不存在的域将被忽略,[num | false]
