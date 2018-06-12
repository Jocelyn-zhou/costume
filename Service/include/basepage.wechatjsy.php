<?php
include_once(SITE_ROOT.'/include/basepage.php');
include_once(SITE_ROOT.'/weixin_kj/wechat/lanewechat.php');
//include_once(SITE_ROOT.'/redpacket/wechat/lanewechat.php');
$baseMember=array();
if (strpos("|test|","|".$baseHeader['api_type']."|")===false)
{
    $sessionid=isset($_SESSION[WEIXIN_JSY])?$_SESSION[WEIXIN_JSY]:"";
    //$sessionid='oJAZtvwHHkVpi7R2ip5EuFtEpnME';
    if(empty($sessionid)){Oauth();exit();}//output_json_error("您还未登陆,立即登录",-1);//What do you want to do?
    if(!$baseMember=$baseMemCache->Get($sessionid)){//output_json_error("账号异常，请重新登录",-1);
      $WeChatOauthModel =Common_IncludeModel('WeChatOauthModel',1);
      $baseMember=$WeChatOauthModel->Get(array('o_state'=>1,'o_openid'=>$sessionid));
      if($baseMember==null){Oauth();exit;}//output_json_error("账号异常，请重新登录",-1);
      $userdata = \LaneWeChat\Core\UserManage::getUserInfo($sessionid);
      if(isset($userdata['subscribe']) && $baseMember['subscribe'] != $userdata['subscribe']){
        $WeChatOauthModel->Update(array('subscribe'=>$userdata['subscribe']),array('o_state'=>1,'o_openid'=>$sessionid));
        $baseMember['subscribe'] = $userdata['subscribe'];
      }
      $baseMemCache->Set($sessionid,$baseMember,1200);
    }
     //var_dump($baseMemCache->Get($sessionname));
     //var_dump($sessionid);
}
//微信授权
function Oauth(){
    \LaneWeChat\Core\WeChatOAuth::getCode('oauth.php?url='.CurPageURL(), $state=1, $scope='snsapi_userinfo');//snsapi_base
}

//生成二维码，返回图片地址
//$sceneId 非0整数
//$qrtype 1为临时二维码 2为永久二维码
function GetQrCode($sceneId,$qrtype=1){
   $dir=(int)(intval($sceneId)/1000);
   $file_dir=dirname(dirname(dirname(__FILE__)))."/Media/qrcode/$dir";
   if(!file_exists($file_dir)) Create_folders($file_dir);
   $name=$sceneId.'.png';
   $filename=$file_dir.'/'.$name;
   if(file_exists($filename)){
    if($qrtype==1){
        if(time()-filemtime($filename)>86400){//大于一天重新生成
            $model= \LaneWeChat\Core\Popularize::createTicket($qrtype, 2592000,$sceneId);
            if(!empty($model)){
                \LaneWeChat\Core\Popularize::getQrcode($model['ticket'], $filename);
                
            }
        }
    }else{
        return Common_MediaUrl("/qrcode/$dir/$name");
    }
   }else{
    $model= \LaneWeChat\Core\Popularize::createTicket($qrtype, 2592000,$sceneId);
    if(!empty($model)){
        \LaneWeChat\Core\Popularize::getQrcode($model['ticket'], $filename);
    }
   }
   return Common_MediaUrl("/qrcode/$dir/$name");
}