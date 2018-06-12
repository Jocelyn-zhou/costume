<?php
include_once 'BaseModel.php';
/**
 * 实体类
 */
class LoginLogModel extends BaseModelCard
{
  function __construct()
  {
    parent::__construct('loginlog','login_id');
  }
}