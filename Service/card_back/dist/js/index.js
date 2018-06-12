var _page = getUrl('page');
if (_page == null){
    _page = 1;
}
$(function () {
    //显示昵称
    $(".nickname").text(local_member.user_name);
    //会员列表
    getList(list,"cardlist",_page);
});
//列表
function getList(list,apitype,page,confing,pageClass){
    pageClass = pageClass || ".page";
    page = page || 1;
    _page = page;
    var keyname = $('.keyname').val();
    //获取数据
    requestAPIP(_backapiurl,'page='+page+'&count=20'+'&keyname='+keyname,function(json){
        list(json);
        showPage(json.data[0][0],pageClass,list,apitype);
    },function(json){
        $(".tip").show().text(json.msg);
    },false, { "apitype": apitype});
}
function list(json){
    var list="";
    if (json.data[1] != ''){
        $('.panel-body').css('display','block');
        $('.page-row').css('display','block');
        $('#no-data').css('display','none');
        $.each(json.data[1],function(k,v) {
            //空值替换
            $.each(v,function(item){
                if(v[item] == null || v[item] == "" || v[item] == undefined){
                    v[item]='-';
                }
            });
            list += '<tr>';
            list += '<td>'+v.c_id+'</td>';
            list += '<td>'+v.c_setdate+'</td>';
            list += '<td>'+v.c_number+'</td>';
            list += '<td>'+v.c_name+'</td>';
            list += '<td>'+v.c_tel+'</td>';
            list += '<td>'+v.c_val+'积分'+'</td>';
            if (v.c_state==1){
                list += '<td>'+'正常'+'</td>';
            }else if(v.c_state==2){
                list += '<td>'+'审核失败'+'</td>';
            }
            list +='<td><a class="btn btn-default" href="/pages/card_update.html?c_id='+v.c_id+'">修改</a>';
            list +='<button class="btn btn-default" style="margin-left: 20px" onclick="del(this)" data-id="'+v.c_id+'">删除</button></td>';
            list += "</tr>";
        });
        $("table tbody").html(list);
    }else if(json.data[1] == ""){
        $('.panel-body').css('display','none');
        $('.page-row').css('display','none');
        $('#no-data').css('display','block');
    }
}
//检索
$('#search').click(function () {
    getList(list,"cardlist",_page);
});
//新建
$('.cardnew').click(function () {
    var url = window.location.href='./pages/card_add.html';
    setTimeout(url,2000);
});
//删除
var c_id = 0;
function del(obj) {
    $(".confirmBox").show();
    c_id = $(obj).attr("data-id");
}
$(".sure").click(function(){
    $(".confirmBox").hide();
    requestAPIP(_backapiurl,'c_id='+c_id,function (json) {
        if(json.code==0){
            fun_message(json.msg);
            var url = "window.location.href='index.html'";
            setTimeout(url,2000);
        }
    },function (json) {
        fun_message(json.msg);
    },false,{"apitype":"cardstate"});
});
//取消
$(".cancel").click(function(){
    $(".confirmBox").hide();
});