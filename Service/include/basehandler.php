<?php

include_once(SITE_ROOT.'/include/common.php');
include_once(SITE_ROOT.'/include/valid.php');
include_once(SITE_ROOT.'/include/language.php');
include_once  'memcache.class.php';
$headers=getallheaders();
$baseMemCache=new MemCaches();
$baseHeader = array('api_type' =>strtolower(isset($headers['apitype'])?$headers['apitype']:""));
if(empty($baseHeader['api_type']))
  $baseHeader['api_type']=strtolower(isset($_REQUEST['apitype'])?$_REQUEST['apitype']:"");
if(empty($baseHeader['api_type'])) output_json_error("账号异常，请重新登录",-1);
//app 0未知 1智行箱 2初蓝 3hr
$baseHeader['app'] =isset($headers['app'])?$headers['app']:"";
if(empty($baseHeader['app']))
  $baseHeader['app']=isset($_REQUEST['app'])?$_REQUEST['app']:"0";
//language 1英文 2中文
$baseHeader['language'] =isset($headers['language'])?$headers['language']:"";
if(empty($baseHeader['language']))
  $baseHeader['language']=isset($_REQUEST['language'])?$_REQUEST['language']:"2";
$baseHeader['lan']=Language($baseHeader['language']);
//m_id
$baseHeader['member'] =isset($headers['member'])?$headers['member']:"";
if(empty($baseHeader['member']))
  $baseHeader['member']=isset($_REQUEST['member'])?$_REQUEST['member']:"";
//access_token
$baseHeader['access_token'] =isset($headers['token'])?$headers['token']:"";
if(empty($baseHeader['access_token']))
  $baseHeader['access_token']=isset($_REQUEST['token'])?$_REQUEST['token']:"";
//access_token
if(empty($baseHeader['access_token']))
  $baseHeader['access_token'] =isset($headers['access_token'])?$headers['access_token']:"";
if(empty($baseHeader['access_token']))
  $baseHeader['access_token']=isset($_REQUEST['access_token'])?$_REQUEST['access_token']:"";
//terminal
$baseHeader['terminal'] =isset($headers['terminal'])?$headers['terminal']:"";
if(empty($baseHeader['terminal']))
  $baseHeader['terminal']=isset($_REQUEST['terminal'])?$_REQUEST['terminal']:"未知";
//devicename
$baseHeader['devicename'] =isset($headers['devicename'])?$headers['devicename']:"";
if(empty($baseHeader['devicename']))
  $baseHeader['devicename']=isset($_REQUEST['devicename'])?$_REQUEST['devicename']:"";

$api_param="";
//sql防注入
if($_REQUEST){
  foreach ($_REQUEST as $key => $value) {
    if($key=='pagetype'||$key=='apitype'||$key=='ad_header'||$key=='up_api' || $key=='a_content')continue;
    if(isSqlInject($key)||isSqlInject($value))output_json_error('请求内容包含非法信息'.$key.'|'.$value,-1);
    $api_param=$api_param."&$key=".$value;
  }
}

$ApiModel=Common_IncludeModel('ApiLogModel','card');
$php_self=substr($_SERVER["REQUEST_URI"],0,strpos($_SERVER["REQUEST_URI"],'?'));//
$ApiModel->Create(array('m_id'=>empty($baseHeader['member'])?0:$baseHeader['member'],
        'api_url'=>$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'],//.(empty($php_self)?$_SERVER['PHP_SELF']:$php_self),
        'api_head'=>'{"apitype":'.$baseHeader['api_type'].',"member":'.$baseHeader['member'].',"access_token":'.$baseHeader['access_token'].'}',
        'api_app'=>$baseHeader['app'],
        'api_param'=>trim($api_param,'&')));//$_SERVER["QUERY_STRING"]



