$(function () {
    //显示昵称
    $(".nickname").text(local_member.user_name);
});
$('#save').click(function () {
    var c_number = $('#c_number').val();
    var c_name = $('#c_name').val();
    var c_tel = $('#c_tel').val();
    if (c_number == ""){
        fun_message('请填写会员卡号');
        return;
    }
    requestAPIP(_backapiurl,'c_number='+c_number+'&c_name='+c_name+'&c_tel='+c_tel,function (json) {
        if(json.code==0){
            fun_message(json.msg);
            var url = "window.location.href='../index.html'";
            setTimeout(url,2000);
        }
    },function (json) {
        fun_message(json.msg);
    },false,{"apitype":"cardadd"});
});

