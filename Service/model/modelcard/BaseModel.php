<?php
 $site_root = dirname(dirname(dirname(__FILE__)));
include_once  $site_root.'/include/model.class.php';

 class BaseModelCard extends ModelClass
{
  function __construct($tablename="",$primarykey='')
  {
//    parent::__construct($tablename,$primarykey,array('table'=>"costume"));
    parent::__construct($tablename,$primarykey,array('host'=>"localhost",'user'=>"root",'password'=>"root",'table'=>"costume",'port'=>3306));

  }
}
