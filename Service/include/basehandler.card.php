<?php
include_once('../include/basehandler.php');
session_start();
$baseUser=array();
if (strpos("|reg|logout|login|passforget|test|","|".$baseHeader['api_type']."|")===false&&strpos($baseHeader['api_type'],"dropdown")===false)
{
    if(empty($baseHeader['member']))output_json_error("您还未登陆,立即登录",-1);//What do you want to do?
    if(empty($baseHeader['access_token']))output_json_error("您还未登陆,立即登录",-1);//What do you want to do?
    $sessionname='card_user'.$baseHeader['member'];
    $baseUser=isset($_SESSION[$sessionname])?$_SESSION[$sessionname]:"";
    if(empty($baseUser)){//output_json_error("账号异常，请重新登录",-1);
      $UsersModel =Common_IncludeModel('UsersModel','card');
      $baseUser=$UsersModel->Get(array('user_id'=>$baseHeader['member'],'user_state'=>1));
//        $baseUser=$UsersModel->Get(array('user_id'=>2,'user_state'=>1));
      if($baseUser==null) output_json_error("账号异常，请重新登录",-1);
      $_SESSION[$sessionname]=$baseUser;
    }


//    if($baseUser['access_token']!=$baseHeader['access_token']) output_json_error("账号在其它地方登录，请重新登录",-3);
//    if(empty($baseUser['rg_id'])) output_json_error("账号尚未分配角色，请联系管理员",-2);
}