<?php
include_once "BaseModel.php";

class CardModel extends BaseModelCard {
    function __construct()
    {
        parent::__construct('card', 'c_id');
    }

    function Pages($page=1,$count=20,$keyname){
        $pagestart=($page-1)*$count;
        $wheres = empty($keyname) ? "" : "and c_number like '%$keyname%' or c_name like '%$keyname%' ";
        $param= array('select'=>'*',
            'where'=>"1=1 $wheres and c_state<3",
            'order'=>' ',
            'limit'=>"limit $pagestart,$count",
            'page'=>"$page as page,$count as count");
        return parent::ListParamBack($param);
    }
    function IdList(){
        return parent::ListSql("select c_id,c_number from card where c_state=1 order by c_id desc");
    }
    function IdsByKey($name){
        $n = trim($name);
        return parent::ListSql("select c_id from card where c_state=1 and c_name='$n' or c_number='$n'");
    }
}