var _base = 1;
//后台地址
var _backurl = "http://cardback.local";//local
//后台接口地址
var  _apiurl= "http://cloth.local";//local
var _backapiurl = _apiurl+"/cloth.php";//
//后台上传图片接口地址
var _mediaurl = _apiurl+"/hop_media.php";
//图片路径地址
var _media = "http://hopmedia.local";

var local_member="";

if(localStorage.getItem("local_member"))
    local_member=JSON.parse(localStorage.getItem("local_member"));
if(!local_member && window.location.href != _backurl + "/login.html"){
    local_member={'user_id':0,'access_token':''};
    window.parent.location.href="/login.html";
}
// 退出登陆
$(".out").click(function(){
    localStorage.clear();
    window.location.href=_backurl + "/login.html";
});

$(document).ready(function () {
    //计算padding
    $('article').css({ 'padding-top': $('header').outerHeight() + "px" });
    //改变窗体执行
    $(window).resize(function () {
        $('article').css({ 'padding-top': $('header').outerHeight() + "px" });
    });
    //头部返回上一页事件
    $(".back").click(function () { window.history.back(-1); });
});
if(typeof(mui) != "undefined"){
    mui.init({
        keyEventBind: {
            backbutton: true  //打开back按键监听
        }
    });
// var page = mui.preload({
// url:new-page-url,
// id:new-page-id,//默认使用当前页面的url作为id
// styles:{},//窗口参数
// extras:{}//自定义扩展参数
// });
    mui.back = function () {
        var pageurl= window.location.href;
        if(pageurl.indexOf('index')>-1){
            fun_confirm('确认关闭当前窗口?',function (index) {
                plus.runtime.quit();//退出APP
            });
        }else if(pageurl.indexOf('payresult')>-1){
            window.location.href ="index.htm";
        }
        else {
            history.back();
        }
    }
}
String.prototype.regex = function (regexString) { return regexString.test(this); }
//是否为空或者全部都是空格
String.prototype.isNull = function () { return this.regex(/^\s*$/); }
//是否为空或者全部都是空格
String.prototype.isNotNull = function () { return !this.isNull(); }
//是否为空####
String.prototype.isIndex0 = function () { return this == "###" ? false : true; }
//是否为IP
String.prototype.isIP = function () { return this.regex(/^((2[0-4]\d|25[0-5]|[01]?\d\d?)\.){3}(2[0-4]\d|25[0-5]|[01]?\d\d?)$/); }
//是否为邮箱
String.prototype.isEmail = function () { return this.regex(/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/); }
//是否符合整数格式
String.prototype.isInteger = function () { return this.regex(/^[-]?\d+$/); }
//是否为正整数
String.prototype.isNumber = function () { return this.regex(/^\d+$/); }
//视力验证
String.prototype.isVision = function () { return this.regex(/^[1-5].\d$/); }
//是否是带小数的数字格式,可以是负数
String.prototype.isDecimal = function () { return this.regex(/^[-]?\d+(.\d+)?$/); }
//是否符合金额格式(格式定义为带小数的正数，小数点后最多2位 )
String.prototype.isMoney = function () { return this.regex(/^\d{1,8}(,\d{3})*(\.\d{1,2})?$/); }
//是否符合手机号格式
String.prototype.isMobile = function () { return this.regex(/^1(3|4|5|7|8)\d{9}$/); }
//是否为端口格式
String.prototype.isPort = function () { return (this.isNumber() && this < 65536); }
//是否只由英文字母和数字和下划线组成
String.prototype.isNumberOr_Letter = function () { return this.regex(/^[0-9a-zA-Z\_]+$/); }
//是否只由英文字母和数字组成
String.prototype.isNumberOrLetter = function () { return this.regex(/^[0-9a-zA-Z]+$/); }
//是否只由汉字、字母、数字组成下划线
String.prototype.isChinaOrNumbOrLett = function () { return this.regex(/^[0-9a-zA-Z\_\-\u4e00-\u9fa5]+$/); }
//是否只由汉字、字母组成
String.prototype.isChinaOrLett = function () { return this.regex(/^[a-zA-Z\u4e00-\u9fa5]+$/); }
//是否电话号码格式
String.prototype.isPhone = function () { return this.regex(/^((0\d{2,3}-)?\d{7,8}(-\d{1,4})?|(400|800)\d{7})$/); }
//是否传真号码格式
String.prototype.isFax = function () { return this.regex(/^(86\-)?(\d{2,4}-)?([2-9]\d{6,7})+(-\d{1,4})?$/); }
//是否电话号码或手机格式
String.prototype.isPhoneOrMobile = function () { return (this.isPhone() || this.isMobile()); }
//是否存在两个以上汉字
String.prototype.isTwoChinese = function () { return this.regex(/[\u4e00-\u9fa5]+.*[\u4e00-\u9fa5]/); }
//是否为网址格式
String.prototype.isWebUrl = function () { return this.regex(/^(http|https|ftp):\/\/([\w-]+\.)+[\w-]+([\/\:][\w- .\/?%&=\;#\*\+]*)?$/); }
//是否为身份证格式|身份证号码为15位或者18位，15位时全为数字，18位前17位为数字，最后一位是校验位，可能为数字或字符X。
String.prototype.isPID = function () { return this.regex(/(^\d{15}$)|(^\d{17}([0-9]|X|x)$)/); }
//是否为区号个格式
String.prototype.isCode = function () { return this.regex(/^0\d{2,3}$/); }
//是否为有效天数
String.prototype.isOverDay = function () { return this.regex(/^(3[0-5]\d|36[0-5]|[0-2]?\d\d?)$/); }
//是否有包含特殊字符
String.prototype.isSpChar = function () { return !this.regex(/(<|>)/); }
String.prototype.isScript = function () { return this.regex(/(<[\/]?script.*>)/i); }
//是否为邮政编码格式
String.prototype.isZip = function () { return this.regex(/^\d{6}$/); }
//是否为网址格式
String.prototype.isUrl = function () { return this.regex(/^([\w-]+\.)+[\w-]+(\/[\w- .\/?%&=]*)?$/); }
//是否为车牌号
String.prototype.isCarNo = function () { return this.regex(/^[\u4e00-\u9fa5]{1}[A-Za-z]{1}[A-Za-z0-9]{5}$/); }
/*字符床格式化*/
String.prototype.format = function (args) {
    if (arguments.length == 0) return this;
    var str = this;
    if (arguments.length == 1 && typeof (args) == "object") {
        for (var key in args) { str = str.replace(eval('/\\{' + key + '\\}/g'), args[key]); }
    } else {
        for (var i = 0; i < arguments.length; i++) {
            if (arguments[i] == undefined) return "";
            str = str.replace(eval('/\\{' + i + '\\}/g'), arguments[i]);
        }
    }
    return str;
}

/*时间格式化*/
Date.prototype.format = function (format) {
    var o = {
        "M+": this.getMonth() + 1,
        "d+": this.getDate(),
        "H+": this.getHours(),
        "m+": this.getMinutes(),
        "s+": this.getSeconds(),
        "q+": Math.floor((this.getMonth() + 3) / 3),
        "S": this.getMilliseconds()
    }
    if (/(y+)/.test(format)) {
        format = format.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
    }
    for (var k in o) {
        if (new RegExp("(" + k + ")").test(format)) {
            format = format.replace(RegExp.$1, RegExp.$1.length == 1 ? o[k] : ("00" + o[k]).substr(("" + o[k]).length));
        }
    }
    return format;
}
//验证方法
var fun_validate = function (obj, error, msg) {
    if (error) {
        fun_message(msg);
        obj.focus();
    }
    return error;
}
//提示窗
// function fun_message(msg) {
//     layer.open({
//         content: msg,
//         skin: 'msg',
//         time: 2 //2秒后自动关闭
//     });
// }
function fun_message(msg){
    layer.msg(msg,{offset:'200px',time:2000});
}
//询问框
function fun_confirm(msg, yes,cancel) {
    //询问框
    layer.open({
        content: msg,
        btn: ['取消','确定'],//确认和取消按钮顺序调反，事件相应调反
        yes: function (index) {
            //此处执行取消事件
            if(cancel)cancel();
            layer.close(index);
        },
        no: function(index){
            //此处执行确定事件
            if(yes)yes();
        }
    });
}
//内容框
function fun_html(html) {
    var _layer = layer.open({
        className: 'popuo-login',
        content: html
    });
    $('.layui-m-layercont').css({ 'padding': '5%' });
    return _layer;
}
//异步提交数据
function requestAPI(requestURL, requestData, successFun, errorFun, async, heads) {
    var jsonFun = new JsonFun(successFun, errorFun);
    $.ajax({
        url: requestURL,
        cache: false,
        headers: heads,
        type: "POST",
        data: requestData + "&n=" + new Date().getSeconds(),
        dataType: "json",
        success: jsonFun.success,
        error: jsonFun.error,
        async: async != false
    });
}
//异步提交数据
function requestAPIP(requestURL, requestData, successFun, errorFun, async, heads) {
    var _data= (heads&&heads.apitype!=undefined?"apitype="+heads.apitype:"")+"&member="+((local_member.user_id==undefined || local_member.user_id=='null' || local_member.user_id==null)?0:local_member.user_id)+"&token="+local_member.access_token+ (requestData!=''?"&"+requestData:"");
    //console.log("requestAPIP:["+requestURL+"?"+_data+"&jsoncallback=success_jsonpcallback]");
    var jsonFun = new JsonFun(successFun, errorFun);
    $.ajax({
        url: requestURL,
        cache: false,
        headers: heads,
        type: "POST",
        data:_data,
        dataType: "jsonp",
        jsonp: 'jsoncallback',
        jsonpCallback:"success_jsonpcallback",
        contentType: "application/json;utf-8",
        success: jsonFun.success,
        error: jsonFun.error,
        async: async != false
    });
}
//异步提交数据 测试接口使用
function testAPIP(requestURL, requestData, successFun, errorFun, async, heads) {
    var _data= requestData + (heads&&heads.apitype!=undefined?"&apitype="+heads.apitype:"")+"&member="+heads.member+"&token="+heads.access_token;
    //console.log("requestAPIP:["+requestURL+"?"+_data+"&jsoncallback=success_jsonpcallback]");
    var jsonFun = new JsonFun(successFun, errorFun);
    $.ajax({
        url: requestURL,
        cache: false,
        headers: heads,
        type: "POST",
        data:_data,
        dataType: "jsonp",
        jsonp: 'jsoncallback',
        jsonpCallback:"success_jsonpcallback",
        contentType: "application/json;utf-8",
        success: jsonFun.success,
        error: jsonFun.error,
        async: async != false
    });
}
//异步提交数据 测试接口微信验证
function wechatAPIP(requestURL, requestData, successFun, errorFun, async, heads) {
    var _data= requestData + (heads&&heads.apitype!=undefined?"&apitype="+heads.apitype:"")+"&member="+heads.openid
    //console.log("requestAPIP:["+requestURL+"?"+_data+"&jsoncallback=success_jsonpcallback]");
    var jsonFun = new JsonFun(successFun, errorFun);
    $.ajax({
        url: requestURL,
        cache: false,
        headers: heads,
        type: "POST",
        data:_data,
        dataType: "jsonp",
        jsonp: 'jsoncallback',
        jsonpCallback:"success_jsonpcallback",
        contentType: "application/json;utf-8",
        success: jsonFun.success,
        error: jsonFun.error,
        async: async != false
    });
}
//异步回调函数
function JsonFun(successFun, errorFun) {
    return {
        success: function (json) {

            if(json.code==-1){
                fun_message(json.msg);
                var i = 2;
                var t = setInterval(function(){
                    if (i == 0) {
                        clearInterval(t);
                        // window.parent.location.href='/login.html';
                        return;
                    }
                    i--;
                }, 1000);
                // setTimeout(function(){
                //     // window.parent.location.href='/login.html';
                // },2000);
            }else if(json.code==-2){
                fun_message(json.msg);
                //window.parent.location.href='/login.html';
            }else if(json.code==-3){
                fun_message(json.msg);
                setTimeout(function(){
                    window.parent.location.href='/login.html';
                },1000);

            }
            if (json != undefined && json.code!=0) {
                if (errorFun != undefined) {
                    errorFun(json);
                }
            }
            else {

                if(json.data.member){
                    localStorage.setItem("local_member",JSON.stringify(json.data.member));
                    if(json.data.menu){
                        localStorage.setItem("local_menu",JSON.stringify(json.data.menu));
                    }
                }
                successFun(json);
            }
        },
        error: function (XMLHttpRequest) {
            var desc = XMLHttpRequest.responseText;
            errorFun({'msg':desc,'code':1});
        }
    };
}


/*获取当前链接的QueryString值*/
function getQueryString(name, url, isToLocaleLowerCase) {
    var reg = new RegExp("(^|&)" + name.toLocaleLowerCase() + "=([^&]*)(&|$)");

    url = url != null && url != undefined ? url : window.location.search;

    if (url.trim().length <= 0) {
        return "";
    }

    var r = (isToLocaleLowerCase == undefined || isToLocaleLowerCase == true) ? (url).replace(RegExp("(.*)\\?", "g"), "").toLocaleLowerCase().match(reg) : (url).replace(RegExp("(.*)\\?", "g"), "").match(reg);
    if (r != null) {
        return decodeURIComponent(r[2]);
    }
    return "";
}

//获取url中的参数
function getUrl(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
    var r = window.location.search.substr(1).match(reg);  //匹配目标参数
    if (r != null) return unescape(r[2]); return null; //返回参数值
}

//弹窗提醒
function cpm(data){
    $(".promptBox").text(data);
    $('.promptBox').fadeIn(300);
    setTimeout('$(\'.promptBox\').fadeOut()',1000);
}

// // 验证权限
// var r_id = localStorage.getItem("r_id");
// if(window.location.href != "http://back.57mz.com/login.html"){
//     //没有登录直接跳转回login页面
//     var str = window.location.href;
//     console.log(str.indexOf("main"))
//     if(r_id == "null" || r_id == "" || r_id == null || str.indexOf("main") > 0){
//         window.parent.location.href="/login.html";
//     }
// }
