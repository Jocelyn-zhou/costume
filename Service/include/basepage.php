<?php
include_once(SITE_ROOT.'/include/common.php');
include_once(SITE_ROOT.'/include/valid.php');
include_once  'memcache.class.php';
$headers=getallheaders();
$baseMemCache=new MemCaches();
$baseHeader = array('api_type' =>strtolower(isset($headers['apitype'])?$headers['apitype']:""));
if(empty($baseHeader['api_type']))
  $baseHeader['api_type']=strtolower(isset($_REQUEST['apitype'])?$_REQUEST['apitype']:"");
//m_id
$baseHeader['member'] =isset($headers['member'])?$headers['member']:"";
if(empty($baseHeader['member']))
  $baseHeader['member']=isset($_REQUEST['member'])?$_REQUEST['member']:"";

//sql防注入
if($_REQUEST){
  foreach ($_REQUEST as $key => $value) {
    if($key=='pagetype'||$key=='apitype')continue;
    if(isSqlInject($key)||isSqlInject($value))output_json_error('请求内容包含非法信息'.$key.'|'.$value,-1);
  }
}