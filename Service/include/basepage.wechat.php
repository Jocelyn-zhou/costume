<?php
include_once(SITE_ROOT.'/include/basepage.php');
include_once(SITE_ROOT.'/sdk/wechat/lanewechat.php');
$baseMember=array();
if (strpos("|test|","|".$baseHeader['api_type']."|")===false)
{
    $sessionid=isset($_SESSION[MEMBER_CACHEPAGE])?$_SESSION[MEMBER_CACHEPAGE]:"";
    //$sessionid='oJAZtvwHHkVpi7R2ip5EuFtEpnME';
    if(empty($sessionid)){Oauth();exit();}//output_json_error("您还未登陆,立即登录",-1);//What do you want to do?
    if(!$baseMember=$baseMemCache->Get($sessionid)){//output_json_error("账号异常，请重新登录",-1);
      $WeChatOauthModel =Common_IncludeModel('WeChatOauthModel','user');
      $baseMember=$WeChatOauthModel->Get(array('o_state'=>1,'o_openid'=>$sessionid));
      if($baseMember==null){Oauth();exit;}//output_json_error("账号异常，请重新登录",-1);
      $baseMemCache->Set($sessionid,$baseMember,1200);
    }
     //var_dump($baseMemCache->Get($sessionname));
     //var_dump($sessionid);
}
//微信授权
function Oauth(){
    \LaneWeChat\Core\WeChatOAuth::getCode('oauth.php?url='.CurPageURL(), $state=1, $scope='snsapi_userinfo');//snsapi_base
}
//生成临时二维码，返回图片地址
//$sceneId 非0整数
function GetQrCode($sceneId){
   $dir=(int)(intval($sceneId)/1000);
   $file_dir=dirname(dirname(dirname(__FILE__)))."/Media/qrcode/$dir";
   if(!file_exists($file_dir)) Create_folders($file_dir);
   $name=$sceneId.'.png';
   $filename=$file_dir.'/'.$name;
   if(file_exists($filename)){
    if(time()-filemtime($filename)>86400){//大于一天重新生成
        $model= \LaneWeChat\Core\Popularize::createTicket(1, 2592000,$sceneId);
        if(!empty($model)){
            \LaneWeChat\Core\Popularize::getQrcode($model['ticket'], $filename);
        }
    }
   }else{
    $model= \LaneWeChat\Core\Popularize::createTicket(1, 2592000,$sceneId);
    if(!empty($model)){
        \LaneWeChat\Core\Popularize::getQrcode($model['ticket'], $filename);
    }
   }
   return Common_MediaUrl("/qrcode/$dir/$name");
}