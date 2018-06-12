<?php
include_once 'BaseModel.php';
/**
 * 实体类
 */
class ApiLogModel extends BaseModelCard
{
  function __construct()
  {
    parent::__construct('apilog','api_id');
  }
}