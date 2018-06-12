<?php
header("Access-Control-Allow-Origin: *");
Header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
include_once('../include/config.php');
include_once(SITE_ROOT . '/include/basehandler.back.php');

switch ($baseHeader['api_type']) {

    case 'test':Test();break;
    case 'login':Login();break;//登录
    case 'userlist':UserList();break;//用户列表
    case 'usercreate':UserCreate();break;//用户注册
    case 'userupdate':UserUpdate();break;//用户更新
    case 'userdetail':UserDetail();break;//用户详情
    case 'memberlist':MemberList();break;//用户列表
    case 'membercreate':MemberCreate();break;//用户注册
    case 'backloglist':BackLogList();break;//日志列表

    //角色分组增删改查（角色配置）
    case 'rolegroupadd':RoleGroupAdd();break;//角色分组添加
    case 'rolegroupdel':RoleGroupDel();break;//角色分组删除
    case 'rolegroupupdate':RoleGroupUpdate();break;//角色分组修改
    case 'rolegrouplist':RoleGroupList();break;//角色分组列表
    case 'rolegroupall':RoleGroupAll();break;//角色分组列表(无分页)
    case 'rolegroupdetail':RoleGroupDetail();break;//角色分组详情

    //菜单权限增删改查
    case 'menupermissionadd':MenuPermissionAdd();break;//菜单权限添加
    case 'menupermissiondel':MenuPermissionDel();break;//菜单权限删除
    case 'menupermissionupdate':MenuPerssionUpdate();break;//菜单权限更新
    case 'menupermissionlist':MenuPermissionList();break;//菜单权限列表
    case 'menupermissionall':MenuPermissionAll();break;//菜单权限列表(无分页)
    case 'menupermissiondetail':MenuPermissionDetail();break;//菜单权限详情
    case 'menuparent':MenuParent();break;//父菜单列表

    //接口测试
    case 'apimenu':ApiMenu();break;//接口目录
    case 'apilist':ApiList();break;//接口列表
    case 'apidetail':ApiDetail();break;//接口详情
    case 'apidataadd':ApiDataAdd();break;//接口增加
}

function ApiDataAdd(){
    $am_id = RequestInt('am_id');
    $ad_title = RequestString('ad_title');
    $ad_url = RequestString('address');
    $ad_desc = RequestString('ad_desc');
    $ad_format = RequestString('ad_format');
    $ad_param = RequestString('ad_param');
    $ad_header = RequestString('ad_header');
    $ad_method = RequestString('ad_method');
    $ad_response = RequestString('ad_response');
    $ad_setdate  = date('Y-m-d H:i:s');
    if(empty($am_id) || empty($ad_title) || empty($ad_url) || empty($ad_desc) || empty($ad_format) || empty($ad_header) || empty($ad_method) || empty($ad_response) || empty($ad_setdate)){
        output_json_error('参数不完整');
    }
    $arr = array(
        'am_id'=>$am_id,
        'ad_title'=>$ad_title,
        'ad_url'=>$ad_url,
        'ad_desc'=>$ad_desc,
        'ad_format'=>$ad_format,
        'ad_param'=>$ad_param,
        'ad_header'=>$ad_header,
        'ad_method'=>$ad_method,
        'ad_response'=>$ad_response,
        'ad_setdate'=>$ad_setdate,
    );
    $ApiDataModel = Common_IncludeModel('ApiDataModel','data');
    $apidata = $ApiDataModel->Create($arr);
    output_json(array('data'=>$apidata));

}

function ApiDetail(){
    $ad_id = RequestInt('ad_id');
    if(empty($ad_id)) output_json_error("参数错误");
    $ApiDataModel = Common_IncludeModel("ApiDataModel",'data');
    $model = $ApiDataModel->Get(array("ad_id"=>$ad_id));
    if(empty($model))
        output_json_error("数据错误，请稍后再试");
    output_json(array("data"=>$model));
}
function ApiMenu(){
    $ApiMenuModel = Common_IncludeModel("ApiMenuModel",'data');
    $list = $ApiMenuModel->BackPage();
    $am_ids="";
    if(!empty($list)){
        foreach($list as $key => $value){
            $am_ids = $am_ids . (strpos($am_ids, "," . $value['am_id'] . ",") === false ? $value['am_id'] . ',' : '');
        }
    }
    $ApiDataModel = Common_IncludeModel("ApiDataModel",'data');
    $listad = $ApiDataModel->ListIDs($am_ids);
    if(!empty($listad)){
        foreach($list as $key=>$value){
            foreach($listad as $keyad=>$valuead){
                if($value["am_id"] == $valuead["am_id"]){
                    $list[$key]['am_count'] = $valuead['count(am_id)'];
                }
            }
        }
    }
    output_json(array("data"=>$list));
}
function ApiList(){
    $am_id = RequestInt('am_id');
    if(empty($am_id)) output_json_error("参数错误");
    $ApiDataModel = Common_IncludeModel("ApiDataModel",'data');
    $list = $ApiDataModel->Apilist($am_id);
    output_json(array("data"=>$list));
}
function MenuPermissionAdd(){
    $mp_group = RequestString('mp_group');
    $mp_name = RequestString('mp_name');
    $mp_url = RequestString('mp_url');
    $parent_id = RequestInt('parent_id');
    if (empty($mp_name) || empty($mp_url)) {
        output_json_error('参数不完整');
    }
    if ($parent_id != 0) {
        if (empty($mp_group)) {
            output_json_error('参数不完整');
        }
    }
    $arr = array(
        'mp_group' => $mp_group,
        'mp_name' => $mp_name,
        'mp_url' => $mp_url,
        'parent_id' => $parent_id,
    );
    $MenuPermissionModel = Common_IncludeModel('MenuPermissionModel', 'user');
    $menuper = $MenuPermissionModel->Create($arr);
    output_json(array('data' => $menuper));
}
function MenuPermissionDel(){
    $mp_id = RequestInt('mp_id');
    if (empty($mp_id))output_json_error('参数不完整');
    $arr = array('mp_id'=>$mp_id);
    $MenuPermissionModel = Common_IncludeModel('MenuPermissionModel','user');
    $menuper = $MenuPermissionModel->Delete($arr);
    output_json(array('data'=>array('result'=>$menuper)),$menuper?'删除成功':'删除失败',$menuper?0:1);
}
function MenuPerssionUpdate(){
    $mp_id = RequestInt('mp_id');
    $mp_group = RequestString('mp_group');
    $mp_name = RequestString('mp_name');
    $mp_url = RequestString('mp_url');
    $parent_id = RequestInt('parent_id');
    if (empty($mp_id) || empty($mp_name) || empty($mp_url)) {
        output_json_error('参数不完整');
    }
    if ($parent_id != 0) {
        if (empty($mp_group)) {
            output_json_error('参数不完整');
        }
    }
    $MenuPermissionModel = Common_IncludeModel('MenuPermissionModel', 'user');
    $menuper = $MenuPermissionModel->Get(array('mp_id' => $mp_id));
    if (empty($menuper)) output_json_error('数据不存在');
    if ($menuper['mp_group'] == $mp_group && $menuper['mp_name'] == $mp_name && $menuper['mp_url'] == $mp_url && $menuper['parent_id'] == $parent_id) {
        output_json_error('数据未修改');
    }
    $arr = array(
        'mp_id' => $mp_id,
        'mp_group' => $mp_group,
        'mp_name' => $mp_name,
        'mp_url' => $mp_url,
        'parent_id' => $parent_id,
    );
    $num = $MenuPermissionModel->UpdateRequest($arr,'mp_id');
    output_json(array('data'=>array('result'=>$num)),$num?'修改成功':'修改失败',$num?0:1);
}

function MenuPermissionList(){
    $page = RequestInt('page', 1);
    $count = RequestInt('count', 20);
    $keyname = RequestString('keyname');
    $MenuPermissionModel = Common_IncludeModel('MenuPermissionModel', 'user');
    $list = $MenuPermissionModel->Pages($page, $count, $keyname);
    if (!empty($list)) {
        foreach ($list[1] as $key => $value) {
            $parentid = $value['parent_id'];
            if ($parentid == 0) {
                $list[1][$key]['parentmenu'] = '顶级菜单';
            } else {
                $parentmenu = $MenuPermissionModel->MenuPerDetail($parentid);
                $list[1][$key]['parentmenu'] = $parentmenu['mp_name'];
            }
        }
    }
    output_json(array('data' => $list));
}

function MenuPermissionDetail(){
    $mp_id = RequestInt('mp_id');
    if (empty($mp_id))output_json_error('参数不完整');
    $MenuPermissionModel = Common_IncludeModel('MenuPermissionModel','user');
    $menuperdetail = $MenuPermissionModel->MenuPerDetail($mp_id);
    $menuparentlist = $MenuPermissionModel->MenuParentList();
    output_json(array('data' => array('menudetail' => $menuperdetail, 'menuparentlist' => $menuparentlist)));
}
function MenuPermissionAll(){
    $MenuPermissionModel = Common_IncludeModel('MenuPermissionModel', 'user');
    $all = $MenuPermissionModel->MenuPerAll();
    $menuparent = $MenuPermissionModel->MenuParentList();
    if (!empty($menuparent)) {
        foreach ($menuparent as $k => $v) {
            $menuparent[$k]['secondmenu'] = $MenuPermissionModel->MenuParentList($v['mp_id']);
        }
    }
    output_json(array('data' => array('info' => $all, 'menulist' => $menuparent)));
}
function MenuParent()
{
    $MenuPermissionModel = Common_IncludeModel('MenuPermissionModel', 'user');
    $menuparentlist = $MenuPermissionModel->MenuParentList();
    output_json(array('data' => $menuparentlist));
}
function RoleGroupDetail()
{
    $rg_id = RequestInt('rg_id');
    if (empty($rg_id)) output_json_error('参数错误');
    $RoleGroupModel = Common_IncludeModel('RoleGroupModel', 'user');
    $roledetail = $RoleGroupModel->RoleGroupDetail($rg_id);
    $MenuPermissionModel = Common_IncludeModel('MenuPermissionModel', 'user');
    $menuparent = $MenuPermissionModel->MenuParentList();
    if (!empty($menuparent)) {
        foreach ($menuparent as $k => $v) {
            $menuparent[$k]['secondmenu'] = $MenuPermissionModel->MenuParentList($v['mp_id']);
        }
    }
    $MenuPermissionRelaModel = Common_IncludeModel('MenuPermissionGroupRelationModel', 'user');
    $menurela = $MenuPermissionRelaModel->All($rg_id);
    output_json(array('data' => array('info' => $roledetail, 'menu' => $menuparent, 'menurela' => $menurela)));
}
function RoleGroupAll()
{
    $RoleGroupModel = Common_IncludeModel('RoleGroupModel', 'user');
    $all = $RoleGroupModel->All();
    output_json(array('data' => $all));
}

function RoleGroupList()
{
    $page = RequestInt('page', 1);
    $count = RequestInt('count', 20);
    $keyname = RequestString('keyname');
    $RoleGroupModel = Common_IncludeModel('RoleGroupModel', 'user');
    $list = $RoleGroupModel->Pages($page, $count, $keyname);
    output_json(array('data' => $list));
}

function RoleGroupUpdate()
{
    $rg_id = RequestInt('rg_id');
    $rg_name = RequestString('rg_name');
    $mp_id = RequestString('mp_id');
    if (empty($rg_id)) output_json_error('参数不完整');
    if (empty($rg_name)) output_json_error('数据不得为空！');
    if (empty($mp_id)) output_json_error('参数不完整');

    $RoleGroupModel = Common_IncludeModel('RoleGroupModel', 'user');
    $groupmodel = $RoleGroupModel->Get(array('rg_id' => $rg_id));
    if (empty($groupmodel)) output_json_error('数据不存在');
    $arr = array(
        'rg_id' => $rg_id,
        'rg_name' => $rg_name,
    );
    $num = $RoleGroupModel->UpdateRequest($arr, 'rg_id');

    $MenuPermissionRelaModel = Common_IncludeModel('MenuPermissionGroupRelationModel', 'user');
    $MenuPermissionRelaModel->Delete(array('rg_id' => $rg_id));
    $mp_ids = explode(',', $mp_id);
    for ($i = 0; $i < count($mp_ids); $i++) {
        if (!empty($mp_ids[$i])) {
            $MenuPermissionRelaModel->Create(array('rg_id' => $rg_id, 'mp_id' => $mp_ids[$i]));

        }
    }
    output_json(array('data' => array('result' => $num)), $num ? '修改成功' : '修改失败', $num ? 0 : 1);
}
function RoleGroupDel()
{
    $rg_id = RequestInt('rg_id');
    if (empty($rg_id)) output_json_error('参数不完整');
    $arr = array('rg_id' => $rg_id);
    $RoleGroupModel = Common_IncludeModel('RoleGroupModel', 'user');
    $result = $RoleGroupModel->Delete($arr);

    output_json(array('data' => array('result' => $result)), $result ? '删除成功' : '删除失败', $result ? 0 : 1);
}
function RoleGroupAdd()
{
    $rg_name = RequestString('rg_name');//角色分组名称
    $mp_id = RequestString('mp_id');//权限id
    $c_time = $u_time = date('Y-m-d H:i:s');//创建时间、更新时间
    if (empty($rg_name)) output_json_error('请添加角色分组名称');
    $arr = array(
        'rg_name' => $rg_name,
        'rg_setdate' => $c_time,
        'rg_updatetime' => $u_time,
    );

    $RoleGroupModel = Common_IncludeModel('RoleGroupModel', 'user');
    $rg_id = $RoleGroupModel->Create($arr);

    $MenuPermissionGroupRelationModel = Common_IncludeModel('MenuPermissionGroupRelationModel', 'user');
    $mp_ids = explode(',', $mp_id);
    for ($i = 0; $i < count($mp_ids); $i++) {
        if (!empty($mp_ids[$i])) {
            $MenuPermissionGroupRelationModel->Create(array('rg_id' => $rg_id, 'mp_id' => $mp_ids[$i]));

        }
    }
    output_json(array('data' => $rg_id));
}
function BackLogList(){
    $page = RequestInt('page',1);
    $count = RequestInt('count',20);
    $user_name = RequestString("keyname");
    $JfsLogModel = Common_IncludeModel('JfsLogModel','data');
    $UsersModel = Common_IncludeModel('UsersModel','user');
    $user_ids = "";
    $ListName = $UsersModel->ListName($user_name);
    if(!empty($ListName)){
        foreach($ListName as $keyr => $valuer){
            $user_ids = $user_ids . (strpos($user_ids, "," . $valuer['user_id'] . ",") === false ? $valuer['user_id'] . ',' : '');
        }
    }
    $list = $JfsLogModel->BackPage($page,$count,$user_ids);
    //搜索不到的时候 模拟空值数据发送，便于前端统一接收格式
    if(empty($ListName) && !empty($user_name)){
        $list = array("0"=>array("0"=>array("nums"=>0,"page"=>1,"count"=>10)),"1"=>"");
    }
    $modular = array(
        "worksupdate"=>"箱包大赛作品审核",
        "worksdel"=>"箱包大赛作品删除",
        "expertdel"=>"箱包大赛行业认证删除",
        "expertupdate"=>"箱包大赛行业认证审核",
        "membercreate"=>"app账号注册",
        "usercreate"=>"后台管理系统账号注册",
        "userdel"=>"后台用户删除",
        "memberdel"=>"app用户删除",
        "userupdate"=>"后台用户审核",
        "memberupdate"=>"app用户审核",
        "bluetoothcardupdate"=>"蓝牙名片状态",
        "deviceunbind"=>"设备解除绑定",
        "deviceupdate"=>"设备状态",
        "DeviceBind"=>"设备绑定",
        "reply"=>"反馈回复",
        "dictionarydel"=>"字典状态",
        "dictionaryupdate"=>"字典修改",
        "customeradd"=>"添加客户信息",
        "customerupdate"=>"修改客户信息",
        "customertrackadd"=>"添加客户跟踪信息",
    );
    if(!empty($list)){
        if(empty($user_ids)){
            foreach($list[1] as $key => $value){
                $position = $list[1][$key]["l_position"];
                if(!empty($modular[$position]))
                    $list[1][$key]["l_position"] = $modular[$position];
                $user_ids = $user_ids . (strpos($user_ids, "," . $value['user_id'] . ",") === false ? $value['user_id'] . ',' : '');
            }
        }else{
            foreach($list[1] as $key => $value){
                $position = $list[1][$key]["l_position"];
                if(!empty($modular[$position]))
                    $list[1][$key]["l_position"] = $modular[$position];
            }
        }
        $userlist = $UsersModel->ListIDs($user_ids);
        if(!empty($userlist)){
            foreach($list[1] as $key => $value){
                foreach($userlist as $keyu => $valueu){
                    if($value['user_id'] == $valueu['user_id']){
                        $list[1][$key]['user_name'] = $valueu['user_name'];
                        break;
                    }
                }
            }
        }
    }
    output_json(array('data' => $list));
}
function MemberCreate()
{
    $reg_name = RequestString('reg_name');
    $reg_password = RequestString('reg_password');
    $reg_nickname = RequestString('reg_nickname');
    if (empty($reg_name) || empty($reg_password) || empty($reg_nickname)) output_json_error("信息不完整");
    $RegisterModel = Common_IncludeModel('RegisterModel', 'user');
    $MemberModel = Common_IncludeModel('MemberModel', 'user');
    $model_reg = $RegisterModel->GetloginModel(1, $reg_name);
    if (!empty($model_reg)) output_json_error("该账号已存在,请重新填写");
    $reg_id = $RegisterModel->Create(array('reg_type' => 1, 'reg_name' => $reg_name, 'reg_password' => md5($reg_password)));
    $m_id = 0;
    if ($reg_id > 0)
        $m_id = $MemberModel->Create(array('reg_id' => $reg_id, 'm_nickname' => $reg_nickname));
    if($m_id > 0){
        $msg = "添加app账号：注册ID为".$reg_id;
        backlog($msg,$reg_id);
    }
    output_json(array('data' => $m_id), $m_id > 0 ? '注册成功' : '', $m_id > 0 ? 0 : 1);
}
function UserDetail(){
    $user_id = RequestInt('user_id');
    if (empty($user_id))output_json_error('参数错误');
    $UserModel = Common_IncludeModel('UsersModel','user');
    $userDetail = $UserModel->UserDetail($user_id);
    $UserPermissionGroupModel = Common_IncludeModel('RoleGroupModel', 'user');
    $groupList = $UserPermissionGroupModel->All();
    output_json(array('data'=>array('info'=>$userDetail,'group'=>$groupList)));
}
function UserUpdate(){
    $user_id = RequestInt('user_id');
    $user_name = RequestString('user_name');
    $rg_id = RequestString('rg_id');
    if (empty($user_id) || empty($user_name) || empty($rg_id)) output_json_error('参数错误');
    $UsersModel = Common_IncludeModel('UsersModel', 'user');
    $model = $UsersModel->Get(array('user_id' => $user_id));
    if (empty($model)) output_json_error('数据不存在');
    $UserPermissionGroupModel = Common_IncludeModel('RoleGroupModel', 'user');
    $pergroup = $UserPermissionGroupModel->Get(array('rg_id' => $rg_id));
    if (empty($pergroup)) output_json_error('用户所属角色分组不存在');
    if ($model['user_name'] == $user_name && $model['rg_id'] == $rg_id) output_json_error('数据未修改！');
    $arr = array(
        'user_id' => $user_id,
        'user_name' => $user_name,
        'rg_id' => $rg_id,
    );
    $num = $UsersModel->UpdateRequest($arr,'user_id');
    output_json(array('data'=>array('result'=>$num)),$num?'修改成功':'修改失败',$num?0:1);
}
function UserList()
{
    $page = RequestInt('page', 1);
    $count = RequestInt('count', 20);
    $keyname = RequestString('keyname');
    $UsersModel = Common_IncludeModel('UsersModel', 'user');
    $list = $UsersModel->Pages($page, $count, $keyname);
    if (!empty($list)) {
        foreach ($list[1] as $key => $value) {
            if (!empty($value['rg_id'])){
                $rg_id[] = $value['rg_id'];//角色分组id
            }
        }
        $rg_ids = implode(',', $rg_id);
        $UserPermissionModel = Common_IncludeModel('RoleGroupModel', 'user');
        $userPerGroupList = $UserPermissionModel->ListIDs($rg_ids);
        if (!empty($userPerGroupList)) {
            foreach ($list[1] as $key => $value) {
                foreach ($userPerGroupList as $k => $v) {
                    if ($value['rg_id'] == $v['rg_id']) {
                        //角色分组名称
                        $list[1][$key]['rg_name'] = $v['rg_name'];
                        break;
                    }
                }
            }
        }
    }
    output_json(array('data' => $list));
}
function UserCreate()
{
    $reg_name = RequestString('reg_name');
    $reg_password = RequestString('reg_password');
    $reg_nickname = RequestString('reg_nickname');
    $rg_id = RequestInt('rg_id');//用户所属角色分组id
    $UserPermissionGroupModel = Common_IncludeModel('RoleGroupModel', 'user');
    $pergroup = $UserPermissionGroupModel->Get(array('rg_id' => $rg_id));
    if (empty($pergroup)) output_json_error('用户所属角色分组不存在');
    if (empty($reg_name) || empty($reg_password) || empty($reg_nickname || empty($upg_id))) output_json_error("信息不完整");
    $RegisterModel = Common_IncludeModel('RegisterModel', 'user');
    $UsersModel = Common_IncludeModel('UsersModel', 'user');
    $model_reg = $RegisterModel->GetloginModel(0, $reg_name);
    if (!empty($model_reg)) output_json_error("该账号已存在,请重新填写");
    $reg_id = $RegisterModel->Create(array('reg_type' => 0, 'reg_name' => $reg_name, 'reg_password' => md5($reg_password)));
    $user_id = 0;
    if ($reg_id > 0)
        $user_id = $UsersModel->Create(array('reg_id' => $reg_id, 'user_name' => $reg_nickname, 'rg_id' => $rg_id));
    if ($user_id > 0) {
        $msg = "添加后台账号：注册ID为" . $reg_id;
//        backlog($msg, $reg_id);
    }
    output_json(array('data' => $user_id), $user_id > 0 ? '注册成功' : '注册失败', $user_id ? 0 : 1);
}
function Login()
{
    global $baseHeader;
    $reg_name = RequestString('reg_name');
    $reg_password = RequestString('reg_password');
    $crm = RequestString('crm');
    if (empty($reg_name)) output_json_error("请填写账号", 1);
    if (empty($reg_password)) output_json_error("请填写密码", 1);
    $RegisterModel = Common_IncludeModel('RegisterModel', 'user');
    $UsersModel = Common_IncludeModel('UsersModel', 'user');
    $model_reg = $RegisterModel->GetloginModel(0, $reg_name);
    if ($model_reg == null) output_json_error("该账号不存在,请重新填写", 1);
    if ($model_reg['reg_password'] != md5($reg_password)) output_json_error("密码错误,请重新填写", 1);
    $msg = '';
    $model_login = array();
    switch ($model_reg['reg_state']) {
        case 0:
            $msg = '您的账户正处审核阶段，请稍后再尝试登录';
            break;
        case 1:
            $msg = '登录成功';
            $istrue = true;
            $model_user = $UsersModel->Get(array('user_state' => 1, 'reg_id' => $model_reg['reg_id']));
            if ($model_user == null) {
                $msg = '该账号不存在，请注册后再尝试登录';
                $RegisterModel->Delete(array('reg_id' => $model_reg['reg_id']));
                $istrue = false;
                break;
            }
            $UsersModel->Update(array(
                'access_token' => SetAccessToken(),
                'access_token_validity' => date('Y-m-d H:i:s')), array('user_id' => $model_user['user_id']));
            $LoginLogModel = Common_IncludeModel('LoginLogModel', 'user');
            $ip = GetIp();
            $LoginLogModel->Create(array(
                'login_type' => 0,
                'm_id' => $model_user['user_id'],
                'login_ip' => $ip,
                'login_address' => GetIpLookup($ip),
                'login_terminal' => $baseHeader['terminal']
            ));
            $model_login = GetMember($model_user['user_id']);
            $_SESSION['jfs_user' . $model_user['user_id']] = $model_login;
            if($crm==''){
                //获取用户可访问的菜单
                $MenuPermissionModel = Common_IncludeModel('MenuPermissionModel', 'user');
                if ($model_user['rg_id'] != 10000) {
                    $MenuPermissionGroupRelationModel = Common_IncludeModel('MenuPermissionGroupRelationModel', 'user');
                    $menuList = $MenuPermissionGroupRelationModel->Lists(array('rg_id' => $model_user['rg_id']));
                    foreach ($menuList as $l) {
                        $userMenuIds[] = $l['mp_id'];
                    }
                }
                //获取后台配置的所有菜单
                $menuparent = $MenuPermissionModel->MenuParentList();
                $menu = array();
                if (!empty($menuparent)) {
                    foreach ($menuparent as $k => $v) {
                        //获取二级菜单
                        $secondMenu = $MenuPermissionModel->MenuParentList($v['mp_id']);
                        if ($secondMenu) {
                            //循环二级菜单，将拥有相同分组名称的菜单合并成一个数组
                            foreach ($secondMenu as $l) {
                                //没有分组名称的二级菜单将不会显示
                                if ($l['mp_group']) {
                                    //超级管理员获取全部菜单，普通用户获取指定菜单
                                    if ($model_user['rg_id'] == 10000 || in_array($l['mp_id'], $userMenuIds)) {
                                        if (!isset($menu[$k])) {
                                            $menu[$k] = $v;
                                        }
                                        $menu[$k]['group'][$l['mp_group']][] = $l;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            break;
        case 2:
            $msg = '您的账户审核失败，请联系我们的客服';
            break;
        default:
            $msg = '账户不存在，请核实后再次登录';
            break;
    }
    if (isset($menu)){
        output_json(array('member' => $model_login, 'menu' => array_values($menu)), $msg, $istrue ? 0 : 1);
    }else{
        output_json(array('member' => $model_login), $msg, $istrue ? 0 : 1);
    }
}

function GetMember($user_id)
{
    $UsersModel = Common_IncludeModel('UsersModel', 'user');
    $model = $UsersModel->Get(array('user_id' => $user_id));
    return $model;
}