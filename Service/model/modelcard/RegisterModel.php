<?php
include_once 'BaseModel.php';
/**
 * 实体类
 */
class RegisterModel extends BaseModelCard
{
  function __construct()
  {
    parent::__construct('register','reg_id');
  }
  //登录get
  function GetloginModel($reg_type,$reg_name){
      if($reg_type == 0){
          return parent::GetSql("select reg_id,reg_type,reg_password,reg_state from register where reg_state<3 and reg_type=$reg_type and reg_name='$reg_name' limit 1");
      }else{
          return parent::GetSql("select reg_id,reg_type,reg_password,reg_state from register where reg_state<3 and reg_type=$reg_type and acc_tel='$reg_name' limit 1");
      }
  }
  //登录get
  function GetloginQQModel($reg_type,$reg_name){
    return parent::GetSql("select reg_id,reg_type,reg_password,reg_state from register where reg_state<3 and reg_type=$reg_type and acc_qq='$reg_name' limit 1");
  }
  //登录get
  function GetloginWXModel($reg_type,$reg_name){
    return parent::GetSql("select reg_id,reg_type,reg_password,reg_state from register where reg_state<3 and reg_type=$reg_type and acc_wechat='$reg_name' limit 1");
  }
  //登录get
  function GetloginSinaModel($reg_type,$reg_name){
    return parent::GetSql("select reg_id,reg_type,reg_password,reg_state from register where reg_state<3 and reg_type=$reg_type and acc_sina='$reg_name' limit 1");
  }
  //登录get
    function GetlogintwitterModel($reg_type,$reg_name){
        return parent::GetSql("select reg_id,reg_type,reg_password,reg_state from register where reg_state<3 and reg_type=$reg_type and acc_twitter='$reg_name' limit 1");
    }
    //登录get
    function GetloginFBModel($reg_type,$reg_name){
        return parent::GetSql("select reg_id,reg_type,reg_password,reg_state from register where reg_state<3 and reg_type=$reg_type and acc_facebook='$reg_name' limit 1");
    }
    //登录get
    function GetloginGoogleModel($reg_type,$reg_name){
        return parent::GetSql("select reg_id,reg_type,reg_password,reg_state from register where reg_state<3 and reg_type=$reg_type and acc_google='$reg_name' limit 1");
    }
    //登录get
    function GetloginLDModel($reg_type,$reg_name){
        return parent::GetSql("select reg_id,reg_type,reg_password,reg_state from register where reg_state<3 and reg_type=$reg_type and acc_linkedin='$reg_name' limit 1");
    }
  //登录get
  function GetloginEmailModel($reg_type,$reg_name){
    return parent::GetSql("select reg_id,reg_type,reg_password,reg_state from register where reg_state<3 and reg_type=$reg_type and acc_email='$reg_name' limit 1");
  }

  //账号密码查询
    function GetRegName($reg_name){
        return parent::GetSql("select reg_id,reg_password from register where reg_state=1 and reg_type=1 and (reg_name='$reg_name' or acc_email='$reg_name')");
    }
  //登录get
    function GetHopNameModel($reg_type,$reg_name){
        return parent::GetSql("select reg_id,reg_type,reg_password,reg_state from register where reg_state<3 and reg_type=$reg_type and reg_name='$reg_name' limit 1");

    }
    function GetHopTelModel($reg_type,$tel){
        return parent::GetSql("select reg_id,reg_type,reg_password,reg_state from register where reg_state<3 and reg_type=$reg_type and acc_tel='$tel' limit 1");

    }
    //登录get
    function GetHopEmailModel($reg_type,$email){
        return parent::GetSql("select reg_id,reg_type,reg_password,reg_state from register where reg_state<3 and reg_type=$reg_type and acc_email='$email' limit 1");
    }
}
