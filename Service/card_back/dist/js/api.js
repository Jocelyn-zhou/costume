// 登陆接口
$(function(){
    $(document).keydown(function(event) {
        if (event.keyCode == 13) {
            $(".btn").click();
        }
    });

	$("#login").click(function(){
		if($("#username").val() == "" || $("#pwd").val() == ""){fun_message("请填写帐号和密码");return;}
			requestAPIP(_backapiurl,"reg_name="+$("#username").val()+"&reg_password="+$("#pwd").val(),function(json){
				if (json.code==0){
                    fun_message(json.msg);
                    var url = "window.location.href = 'index.html'";
                    setTimeout(url,1000);
                }
			},function(json){
                fun_message(json.msg);
            },false, { "apitype": "login"});
	});

	//添加后台账号
	$(".user_btn").click(function(){
			if($(".username").val() == "" || $(".userpsw").val() == "" || $(".nickname").val() == ""){$(".hid").show().text("请填写帐号和密码");return;}
			requestAPIP(api_url,"reg_name="+$(".username").val()+"&reg_password="+$(".userpsw").val()+"&reg_nickname="+$(".nickname").val(),function(json){
                fun_message(json.msg);
			},function(json){
                fun_message(json.msg);
			},false,{"apitype":"UserCreate"})
	});

});
