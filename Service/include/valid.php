<?php
//正则验证类
//是否是手机号码
function isMobile($val){ return preg_match("/^1[345678]{1}\d{9}$/",$val);}
//是否是邮箱
function isEmail($val){ return preg_match("/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/",$val);}

//是否有sql注入
function isSqlInject($val){

return preg_match("/exec|execute|insert|select|delete|update|alter|create|drop|chr|char|asc|substring|master|truncate|declare|xp_cmdshell|restore|backup|net +user|net +localgroup +administrators|--/i", $val);
    //return preg_match("/select|insert|and|or|update|delete|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile|exec|execute|alter|create|drop|count|chr|char|asc|mid|substring|master|truncate|declare|xp_cmdshell|restore|backup|net +user|net +localgroup +administrators|--/i", $val);
}

function isSymbol($val){
    return preg_match("/[\'.,:;*?~`!@#$%^&+=)(<>{}，。？：；！]|\]|\[|\/|\\\|\"|\|/",$val);
}

