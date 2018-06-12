<?php
header("Content-type: text/html; charset=UTF-8");
date_default_timezone_set('PRC');

//返回json格式正确信息
function output_json($arr='',$msg="成功",$code=0){
  $array = array("msg" => $msg,"code" => $code,"time"=>date('Y-m-d H:i:s'));
  if(is_array($arr)){
    if(array_key_exists("data",$arr)) 
        $array=array_merge($array,$arr);
    else 
        $array=array_merge($array,array('data'=>$arr));
  }
  //$array=preg_replace('(\,\""\w+\"":(null|undefined|\{\}|""{2}|\""\\\/date[^\/]+\/\"")|\""\w+\"":(null|undefined|\{\}|""{2}|\""\\\/date[^\/]+\/\"")\,)','',$array);
  $array=str_replace('setDate','setdate',json_encode($array));
  $jsoncallback = isset($_REQUEST['jsoncallback'])?$_REQUEST['jsoncallback']:"";

//   $myfile = fopen("../newfile.txt", "w") or die("Unable to open file!");
//   fwrite($myfile, $array."\n");
//   fclose($myfile);

  if(empty($jsoncallback))
    echo $array;
  else
    echo "$jsoncallback($array)";
  exit();
}
//返回json格式错误信息
function output_json_error($msg,$code=1){
  $array=urldecode(json_encode(array("msg" => urlencode($msg),"code" => $code,"time"=>date('Y-m-d H:i:s'))));
  $jsoncallback = isset($_REQUEST['jsoncallback'])?$_REQUEST['jsoncallback']:"";
  if(empty($jsoncallback))
    echo $array;
  else
    echo "$jsoncallback($array)";
  exit();
}
//get post请求 接收int类型数据
function RequestInt($key,$value=0){
  return isset($_REQUEST[$key])&& preg_match("/^\d*$/",$_REQUEST[$key]) ?$_REQUEST[$key]: $value;
}
//get post请求 接收string类型数据
function RequestString($key,$value=""){
  return isset($_REQUEST[$key])||!empty($_REQUEST[$key])?$_REQUEST[$key]:$value;
}

//登陆token数据
function SetAccessToken(){
  return md5(uniqid()).uniqid().'wt';
}
//生成验证码
function RandCode($format='ALL',$len=6){
    $is_abc = $is_numer = 0;
    $password = $tmp ='';  
    switch($format){
        case 'ALL':
            $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        break;
        case 'CHAR':
            $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        break;
        case 'NUMBER':
            $chars='0123456789';
        break;
        default :
            $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        break;
    }
    mt_srand((double)microtime()*1000000*getmypid());
    while(strlen($password)<$len){
        $tmp =substr($chars,(mt_rand()%strlen($chars)),1);
        if(($is_numer <> 1 && is_numeric($tmp) && $tmp > 0 )|| $format == 'CHAR'){
            $is_numer = 1;
        }
        if(($is_abc <> 1 && preg_match('/[a-zA-Z]/',$tmp)) || $format == 'NUMBER'){
            $is_abc = 1;
        }
        $password.= $tmp;
    }
    if($is_numer <> 1 || $is_abc <> 1 || empty($password) ){
        $password = RandCode($len,$format);
    }
    return $password;
}
//获取ip地址
function GetIp(){
    $realip = '';
    $unknown = 'unknown';
    if (isset($_SERVER)){
        if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']) && strcasecmp($_SERVER['HTTP_X_FORWARDED_FOR'], $unknown)){
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            foreach($arr as $ip){
                $ip = trim($ip);
                if ($ip != 'unknown'){
                    $realip = $ip;
                    break;
                }
            }
        }else if(isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP']) && strcasecmp($_SERVER['HTTP_CLIENT_IP'], $unknown)){
            $realip = $_SERVER['HTTP_CLIENT_IP'];
        }else if(isset($_SERVER['REMOTE_ADDR']) && !empty($_SERVER['REMOTE_ADDR']) && strcasecmp($_SERVER['REMOTE_ADDR'], $unknown)){
            $realip = $_SERVER['REMOTE_ADDR'];
        }else{
            $realip = $unknown;
        }
    }else{
        if(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), $unknown)){
            $realip = getenv("HTTP_X_FORWARDED_FOR");
        }else if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), $unknown)){
            $realip = getenv("HTTP_CLIENT_IP");
        }else if(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), $unknown)){
            $realip = getenv("REMOTE_ADDR");
        }else{
            $realip = $unknown;
        }
    }
    $realip = preg_match("/[\d\.]{7,15}/", $realip, $matches) ? $matches[0] : $unknown;
    return $realip;
}
//根据ip地址获取城市信息（调用新浪接口）
function GetIpLookup($ip = ''){
    if(empty($ip)){
        $ip = GetIp();
    }
    $res = @file_get_contents('http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=js&ip=' . $ip);
    if(empty($res)){ return false; }
    $jsonMatches = array();
    preg_match('#\{.+?\}#', $res, $jsonMatches);
    if(!isset($jsonMatches[0])){ return false; }
    $json = json_decode($jsonMatches[0], true);
    if(isset($json['ret']) && $json['ret'] == 1){
        $json['ip'] = $ip;
        unset($json['ret']);
    }else{
        return false;
    }
    return $json['province'].$json['city'];
}
//加密解密
function AuthCode($val, $operation = 'DECODE') {
    $string=$operation == 'DECODE'?str_replace('%','+',$val):$val;
    $expiry = 0;
    // 动态密匙长度，相同的明文会生成不同密文就是依靠动态密匙   
    $ckey_length = 4;
    // 密匙   
    $key = md5('thephpissimple');
    // 密匙a会参与加解密   
    $keya = md5(substr($key, 0, 16));
    // 密匙b会用来做数据完整性验证
    $keyb = md5(substr($key, 16, 16));   
    // 密匙c用于变化生成的密文   
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';   
    // 参与运算的密匙
    $cryptkey = $keya.md5($keya.$keyc);
    $key_length = strlen($cryptkey);
    // 明文，前10位用来保存时间戳，解密时验证数据有效性，10到26位用来保存$keyb(密匙b)， 
    //解密时会通过这个密匙验证数据完整性   
    // 如果是解码的话，会从第$ckey_length位开始，因为密文前$ckey_length位保存 动态密匙，以保证解密正确   
    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;   
    $string_length = strlen($string);
    $result = '';
    $box = range(0, 255);
    $rndkey = array();
    // 产生密匙簿
    for($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);   
    }
    // 用固定的算法，打乱密匙簿，增加随机性，好像很复杂，实际上对并不会增加密文的强度
    for($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;   
        $tmp = $box[$i];   
        $box[$i] = $box[$j];   
        $box[$j] = $tmp;   
    }   
    // 核心加解密部分   
    for($a = $j = $i = 0; $i < $string_length; $i++) {   
        $a = ($a + 1) % 256;   
        $j = ($j + $box[$a]) % 256;   
        $tmp = $box[$a];   
        $box[$a] = $box[$j];   
        $box[$j] = $tmp;   
        // 从密匙簿得出密匙进行异或，再转成字符   
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));   
    }
    if($operation == 'DECODE') {  
        // 验证数据有效性，请看未加密明文的格式   
        if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {   
            return substr($result, 26);   
        } else {   
            return '';   
        }   
    } else {   
        // 把动态密匙保存在密文里，这也是为什么同样的明文，生产不同密文后能解密的原因   
        // 因为加密后的密文可能是一些特殊字符，复制过程可能会丢失，所以用base64编码   
        return $keyc.str_replace('+','%',str_replace('=', '', base64_encode($result))); 
    }
}
// 说明：获取完整URL
function CurPageURL() 
{
  $pageURL = 'http';
  if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") 
  {
    $pageURL .= "s";
  }
  $pageURL .= "://";
  if (isset($_SERVER["SERVER_PORT"]) && $_SERVER["SERVER_PORT"] != "80") 
  {
    $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
  }
  else
  {
    $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
  }
  return $pageURL;
}
//返回日期差值
function DateCompare($start,$end){
    return strtotime(date('Y-m-d',strtotime($start)))-strtotime(date('Y-m-d',strtotime($end)));
}
/*
base yoga comm
*/

//修改用户头像路径
function Common_MemberFace($face,$default="/upload/sys/head.png"){
  if(empty($face))$face=$default;
  if(empty($face))return '';
  if(stripos($face,'http')===false){
      return  PATH_APIMEDIA.$face;
  }
  else
    return $face;
}
 /// <summary>
 /// 调整媒体文件路径
 /// </summary>
 /// <param name="face"></param>
 /// <returns></returns>
 function Common_MediaUrl($url)
 {
     if(empty($url))return "";
     if(stripos($url,',')!==false)
     {
         $array =explode(',',trim($url,','));
         $urls = "";
         foreach ($array as $value)
         {
              if(stripos($value,'http')===false)
                 $urls .= PATH_MEDIA . $value . ",";
             else
                 $urls .= $value + ",";
         }
         return trim($urls,',');
     }
     else
     {
         if(stripos($url,'http')===false)
             return PATH_MEDIA . $url;
         else
             return $url;
     }
}
/// <summary>
/// 返回列表媒体文件路径
/// </summary>
/// <param name="url"></param>
/// <returns></returns>
function  Common_MediaUrlList($url)
 {
    $list = array();
     if(empty($url))return "";
     if(stripos($url,',')!==false)
     {
         $array =explode(',',trim($url,','));
         foreach ($array as $value)
         {
           if(stripos($value,'http')===false)
              $list[count($list)]= PATH_MEDIA . $value;
          else
              $list[count($list)]= $value;
         }
     }
     else
     {
       if(stripos($url,'http')===false)
           $list[count($list)]= PATH_MEDIA . $url;
       else
           $list[count($list)]= $url;
     }
     return $list;
 }
 //移除域名
 function Common_RemoveUrl($url){
   if(empty($url))return "";
   if(stripos($url,'http')===false) return $url;
   //移除域名
   return $url;
 }
//文件引入
function Common_IncludeModel($value='',$modeltype='',$tablename='',$primarykey='')
 {
   if(empty($value))return "";
   if(empty($modeltype))return "";
   if($modeltype=='sdk')
    $url='sdk/';
   else
    $url="model/model$modeltype/";
   class_exists($value) or include_once(SITE_ROOT."/$url$value.php");
   if (strpos($value,"/")===false)
        if(empty($tablename)&&empty($primarykey))
            return new $value();
        else
            return new $value($tablename,$primarykey);
   else {
       $news=substr(strrchr($value, "/"), 1);
       if(empty($tablename)&&empty($primarykey))
            return new $news();
        else
            return new $news($tablename,$primarykey);
   }
 }

//字符串替换
function Common_Format() {
   $args = func_get_args();
   if (count($args) == 0) { return;}
   if (count($args) == 1) { return $args[0];}
   $str = array_shift($args);
   $str = preg_replace_callback('/\\{(0|[1-9]\\d*)\\}/', create_function('$match', '$args = '.var_export($args, true).'; return isset($args[$match[1]]) ? $args[$match[1]] : $match[0];'), $str);
   return $str;
}
//创建文件夹
function Create_folders($dir){
    return is_dir($dir) or (create_folders(dirname($dir)) and mkdir($dir, 0777));
}
//getallheaders这个函数只有apache有，nginx没有，所以判断这个函数不存在的时候使用自定义函数，实现同样的作用
if (!function_exists('getallheaders')) {
    function getallheaders() {
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }
}
//设置token
function Common_SetTokenName($key,$id,$apptype){
   return $key.$id.'-'.$apptype;
}

//随机生成32位数字加英文字母的字符串 微信支付商户订单号
function Create_str($strlen){
    //字符串前八位当前日期
    $str = date("Ymd",time());
    $chars = 'qwertyuiopasdfghjklmnbvcxzQWERTYUIOPASDFGHJKLZXCVBNM0123456789';
    for($i = 0; $i < $strlen - 8; $i++){
        $str .= $chars[mt_rand(0,strlen($chars) - 1)];
    }
    //生成的字符串进行md5加密
//    $str = md5($str);
    return $str;
}

//将xml转为array
function FromXml($xml){
    if(!$xml){
        throw new WxPayException("xml数据异常！");
    }
    //将XML转为array
    //禁止引用外部xml实体
    libxml_disable_entity_loader(true);
    $data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    return $data;
}
