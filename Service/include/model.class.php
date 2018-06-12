<?php
include_once  'mysql.class.php';
include_once  'memcache.class.php';
//函数参数$expire -1不缓存 0为永久缓存 >1为缓存时间单位秒
class ModelClass
{
  protected $db;
  protected $tablename;
  protected $primarykey;
  function __construct($tablename="",$primarykey='',$libsql)
  {
    if(empty($libsql['host'])){
      $libsql['host']='localhost';
      $libsql['user']='root';
      $libsql['password']='root';
      $libsql['port']=3306;
    }
    $this->db=new lib_mysqli($libsql['host'],$libsql['user'],$libsql['password'],$libsql['table'],$libsql['port']);//("localhost","root","1qaz2wsx3edc","ykh",3306);
    $this->tablename=$tablename;
    $this->primarykey=$primarykey;
  }
  //获取多条数据
  //params{select,where,order,limit}
  function ListParam($params,$expire=-1)
  {
    //查询到大于号判断为执行最新数据操作
    if(strpos($params['where'],'>')===true){
      $this->db->sql="select * from (select ".$params['select']." from $this->tablename  where ".$params['where']." ". str_replace('desc','asc',strtolower($params['order'])) ." ".$params['limit'].") T ".$params['order']." ;";
    }else
      $this->db->sql="select ".$params['select']." from $this->tablename  where ".$params['where']." ".$params['order']." ".$params['limit'].";";
    //echo $this->db->sql;
    $list=array();
    if($expire>-1){
      $mem=new MemCaches();
      if(!$list=$mem->Get($this->db->sql)){
        $list=$this->db->GetAll();
        if(!empty($list))$mem->Set($this->db->sql,$list,$expire);
      }
    }else  $list=$this->db->GetAll();
    return $list;
  }
  //后台分页获取多条数据
  //params{select,where,order,limit}
  function ListParamBack($params,$expire=-1)
  {
      $this->db->sql="select count(*) as nums,".$params['page']." from $this->tablename where ".$params['where'].";
                      select ".$params['select']." from $this->tablename  where ".$params['where']." ".$params['order']." ".$params['limit'].";";
    //echo $this->db->sql;
    $list=array();
    if($expire>-1){
      $mem=new MemCaches();
      if(!$list=$mem->Get($this->db->sql)){
        $list=$this->db->GetAllList();
        if(!empty($list))$mem->Set($this->db->sql,$list,$expire);
      }
    }else  $list=$this->db->GetAllList();
    return $list;
  }

  //获取多条数据
  function ListSql($sql,$expire=-1)
  {
      $this->db->sql=$sql;
       $list=array();
      if($expire>-1){
        $mem=new MemCaches();
        if(!$list=$mem->Get($this->db->sql)){
          $list=$this->db->GetAll();
          if(!empty($list))$mem->Set($this->db->sql,$list,$expire);
        }
      }else  $list=$this->db->GetAll();
    return $list;
  }
  //获取多条数据
  function Lists($arraywhere,$select='*',$sort='',$expire=-1)
  {
    $where="";
    if($arraywhere!=""){
      foreach ($arraywhere as $key => $value) {
        if(empty($key)||!isset($value))continue;
        if(is_numeric($value))
          $where.= " and $key=$value";
        else
          $where.= " and $key='$value' ";
      }
      $where=rtrim($where,",");
    }
    $orderby =!empty($sort)?"order by $sort":"order by $this->primarykey desc";
    $this->db->sql="select $select from $this->tablename  where 1=1 $where $orderby;";
    //echo $this->db->sql;
     $list=array();
    if($expire>-1){
      $mem=new MemCaches();
      if(!$list=$mem->Get($this->db->sql)){
        $list=$this->db->GetAll();
        if(!empty($list))$mem->Set($this->db->sql,$list,$expire);
      }
    }else  $list=$this->db->GetAll();
    return $list;
  }
  //公用获取单条数据
  function GetSql($sql,$expire=-1)
  {
      $this->db->sql=$sql;
      $list=array();
      if($expire>-1){
        $mem=new MemCaches();
        if(!$list=$mem->Get($this->db->sql)){
          $list=$this->db->GetRow();
          if(!empty($list))$mem->Set($this->db->sql,$list,$expire);
        }
      }else  $list=$this->db->GetRow();
      return $list;
  }
  //公用获取单条数据
  function Get($arraywhere,$select='*',$sort='',$expire=-1)
  {
      $where="";
      if($arraywhere!=""){
        foreach ($arraywhere as $key => $value) {
          if(empty($key)||!isset($value))continue;
          if(is_numeric($value))
            $where.= " and $key=$value";
          else
            $where.= " and $key='$value' ";
        }
        $where=rtrim($where,",");
      }
      $orderby =!empty($sort)?"order by $sort":"order by $this->primarykey desc";
      $this->db->sql="select $select from $this->tablename where 1=1 $where $orderby limit 1;";
      $list=array();
      if($expire>-1){
        $mem=new MemCaches();
        if(!$list=$mem->Get($this->db->sql)){
          $list=$this->db->GetRow();
          if(!empty($list))$mem->Set($this->db->sql,$list,$expire);
        }
      }else  $list=$this->db->GetRow();
      //echo $this->db->sql;
      return $list;
  }
  //公用获取单条数据
  function UpdateSql($sql)
  {
      $this->db->sql=$sql;
      return $this->db->Update();
  }
  //公用更新方法
  function Update($arrayparam,$arraywhere){
    $param="";
    $where="";
    if(!empty($arrayparam)){
      foreach ($arrayparam as $key => $value) {
        if($this->RemoveKey($key))continue;
        if(empty($key)||!isset($value))continue;
        if(is_numeric($value))
          $param.= "$key=$value,";
        else
          $param.= "$key='$value',";
      }
        $param=rtrim($param,",");
    }
    if(!empty($arraywhere)){
      foreach ($arraywhere as $key => $value) {
        if(empty($key)||!isset($value))continue;
        if(is_numeric($value))
          $where.= " and $key=$value";
        else
          $where.= " and $key='$value' ";
      }
      $where=rtrim($where,",");
    }
    $this->db->sql="update $this->tablename set $param where 1=1 $where;";
    return $this->db->Update();
  }
  //更新请求的数据(单个条件)
  function UpdateRequest($reuest,$wherekey){
    $param="";
    $where="";
    if($reuest!=""){
      foreach ($reuest as $key => $value) {
        if($this->RemoveKey($key))continue;
        if(empty($key)||!isset($value))continue;
        if($key==$wherekey){
          if(is_numeric($value))
            $where= "$key=$value";
          else
            $where= "$key='$value'";
        }else {
          if(is_numeric($value))
            $param.= "$key=$value,";
          else
            $param.= "$key='$value',";
          }
      }
    $param=rtrim($param,",");
    if(empty($param)||!isset($where)) return false;
    $this->db->sql="update $this->tablename set $param where $where;";
    return $this->db->Update();
    }
    return false;
  }
  function CreateSql($sql)             
  {
      $this->db->sql=$sql;
      return $this->db->Add();
  }
  //公用添加方法
  function Create($arrayparam){
    //insert into table(id,name) values(1,'asd')
      $paramkey="";
      $paramvalue="";
      if($arrayparam!=""){
        foreach ($arrayparam as $key => $value) {
          if($this->RemoveKey($key))continue;
          if(empty($key)||!isset($value))continue;
          $paramkey.= "$key,";
          if(is_numeric($value))
            $paramvalue.="$value,";
          else{
            if(is_array($value)){
              $val=implode(',',$value);
              $paramvalue.= "'$val',";
            }else
              $paramvalue.= "'$value',";
          }
        }
          $paramkey=rtrim($paramkey,",");
          $paramvalue=rtrim($paramvalue,",");
      }
      $this->db->sql="insert into $this->tablename($paramkey) values($paramvalue);";
      return $this->db->Add();
  }
  //公用删除方法
  function Delete($arraywhere,$wheres=''){
    $where='';
    if($arraywhere!=""){
      foreach ($arraywhere as $key => $value) {
        if(empty($key)||!isset($value))continue;
        if(is_numeric($value))
          $where.= " and $key=$value";
        else
          $where.= " and $key='$value' ";
      }
      $where=rtrim($where,",");
    }else {
      $where=$wheres;
    }
    $this->db->sql="delete from $this->tablename where 1=1 $where;";
    return $this->db->Update();
  }
   //同时执行多条语句
   function ExecLists($sqls)
   {
     $this->db->sql=$sqls;
     return $this->db->UpdateList();
   }

  function RemoveKey($key)
  {
    return $key=='n'||$key=='_'||$key=='apitype'||$key=='jsoncallback'||$key=='member'||$key=='token'||($this->tablename!='member'&&$this->tablename!='users'&&$key=='access_token');
  }
  // function TableIsExits(){
  //   $this->db->sql="select table_name from information_schema.tables where table_name ='$this->tablename'";
  //   $model=$this->db->GetRow();
  //   if(empty($model)||empty($model))
  //   return $list;
  // }
}