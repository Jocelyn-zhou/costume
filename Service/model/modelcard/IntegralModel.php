<?php
include_once 'BaseModel.php';
/**
 * 实体类
 */
class IntegralModel extends BaseModelCard
{
  function __construct()
  {
    parent::__construct('integral','i_id');
  }
  function AllVal($c_id){
      return parent::GetSql("SELECT c_id,SUM(`i_val`) AS val FROM `integral` WHERE `c_id`=$c_id AND `i_state`=1");
  }
    //管理后台列表
    function BackPagesDone($page=1,$count=20,$where){
        $pagestart=($page-1)*$count;
        $wheres = '';
        foreach ($where as $k=>$v){
            if($k == 'c_id'){
                $wheres .= empty($v) ? "" : "and $k in ($v) ";
            }
        }
        $param= array('select'=>'*',
            'where'=>"1=1 $wheres and i_state=1",
            'order'=>'order by i_id desc',
            'limit'=>"limit $pagestart,$count",
            'page'=>"$page as page,$count as count");
        return parent::ListParamBack($param);
    }
}
