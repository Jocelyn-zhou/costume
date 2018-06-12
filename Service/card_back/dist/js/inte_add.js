$(function () {
    //显示昵称
    $(".nickname").text(local_member.user_name);
    requestAPIP(_backapiurl,'',function (json) {
        if(json.code==0){
            var card = json.data;
            $.each(card,function (k,v) {
                $('#c_id').append('<option value="'+v.c_id+'">'+v.c_number+'</option>');
            });
        }
    },function (json) {
        fun_message(json.msg);
    },false,{"apitype":"cardids"});
});
$('#save').click(function () {
    var c_id = $('#c_id').val();
    console.log(c_id);
    var i_note = $('#i_note').val();
    var i_val = $('#i_val').val();
    var code = $('input:radio:checked').val();
    if (c_id == ""){
        fun_message('请选择会员卡号');
        return;
    }
    if (i_val == ""){
        fun_message('请填写积分数值');
        return;
    }
    requestAPIP(_backapiurl,'c_id='+c_id+'&i_val='+i_val+'&i_note='+i_note+'&code='+code,function (json) {
        if(json.code==0){
            fun_message(json.msg);
            var url = "window.location.href='integral.html'";
            setTimeout(url,2000);
        }
    },function (json) {
        fun_message(json.msg);
    },false,{"apitype":"integraladd"});
});

