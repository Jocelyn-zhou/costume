var _page = getUrl('page');
if (_page == null){
    _page = 1;
}
$(function () {
    //显示昵称
    $(".nickname").text(local_member.user_name);
    //会员列表
    getList(list,"integrallist",_page);
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
            list += '<td>'+v.i_id+'</td>';
            list += '<td>'+v.i_setdate+'</td>';
            list += '<td>'+v.c_number+'</td>';
            list += '<td>'+v.c_name+'</td>';
            list += '<td>'+v.c_tel+'</td>';
            list += '<td>'+v.i_val+'积分'+'</td>';
            list += '<td>'+v.i_note+'</td>';
            if (v.i_state==1){
                list += '<td>'+'有效积分'+'</td>';
            }else if (v.i_state==2){
                list += '<td>'+'无效积分'+'</td>';
            }
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
    getList(list,"integrallist",_page);
});
//新建
$('.cardnew').click(function () {
    var url = window.location.href='inte_add.html';
    setTimeout(url,2000);
});