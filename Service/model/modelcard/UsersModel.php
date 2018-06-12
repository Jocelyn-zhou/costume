<?php
include_once 'BaseModel.php';
/**
 * 实体类
 */
class UsersModel extends BaseModelCard
{
  function __construct()
  {
    parent::__construct('users','user_id');
  }
  //////////////////////////////////////////////后台管理接口//////////////////////////////////////////////////////////////////////
  function Pages($page=1,$count=20,$keyname){
     $pagestart=($page-1)*$count;
     $wheres=empty($keyname)?"":" and user_name like '%$keyname%'";
     $param= array('select'=>'*,(select login_setdate from loginlog where login_type=0 and m_id=user_id order by login_id desc limit 1) as logintime',
        'where'=>"1=1 $wheres and user_state<3",
        'order'=>'order by user_id desc',
        'limit'=>"limit $pagestart,$count",
        'page'=>"$page as page,$count as count");
    return parent::ListParamBack($param);
  }

    function ListIDs($user_ids){
        $user_ids=trim($user_ids,',');
        if(empty($user_ids))return array();
        return parent::ListSql("select user_id,user_name from users where user_state=1 and user_id in($user_ids)");
    }

    function ListName($user_name){
        if(empty($user_name))return array();
        return parent::ListSql("select user_id from users where user_name like '%$user_name%'");
    }

    function UserDetail($user_id){
        return parent::GetSql("select * from users where user_id=$user_id");
    }
    function ListByRgids($rg_ids){
        $rg_ids=trim($rg_ids,',');
        if(empty($rg_ids))return array();
        return parent::ListSql("select user_id,user_name from users where user_state=1 and rg_id in($rg_ids)");
    }
    function Users(){
        return parent::ListSql("select user_id,user_name from users where user_state=1");
    }
}
