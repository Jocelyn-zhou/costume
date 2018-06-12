
/**
 * 获取页面数据
 * @param list callback 处理list数据的函数地址
 * @param page int 页码
 * @param config json对象 nums=>总条数,count=>一页显示的条数,page=>当前页码
 * @param pageClass string 页面要放入的div的class
 */
function getList(list,apitype,page,confing,pageClass){
    pageClass = pageClass || ".page";
    page = page || 1;
    //获取数据
    requestAPIP(api_url,"keyname="+ $(".keyname").val()+'&page='+page+'&count=10',function(json){
        list(json);
        showPage(json.data[0][0],pageClass,list,apitype);
    },function(json){
        $(".tip").show().text(json.msg);
    },false, { "apitype": apitype})
}

/**
 * 分页写入数据和写入页码
 * @param config json对象 nums=>总条数,count=>一页显示的条数,page=>当前页码
 * @param pageClass string 页面要放入的div的class
 */
function showPage(config,pageClass,list,apitype){
	//分页
    page = '';
    total = Math.ceil(config.nums / config.count);
    if (config.page==1){
        page += '<li class="paginate_button previous disabled p-empty"><span>上一页</span></li>';
    }
    //上一页
    if(config.page != 1){
        page += '<li class="paginate_button"><a href="javascript:;" class="prev">上一页</a></li>';
    }
    //起始页码
    if(config.page > 3 && total-config.page >= 2){
        var start = config.page - 2;
	}else if(total - config.page < 2 && total - 4 > 0){
        var start = total - 4;
	}else{
	    var start = 1;
	}
	//结束页码
	if(total - config.page >= 2 && config.page >= 3){
	    var end = config.page - 0 + 2;
	}else if(config.page < 3 && total > 0 && total > 5){
	    var end = 5;
	}else{
        var end = total;
	}
    //中间页码
    for(var i = start; i <= end; i++){
        if(config.page == i){
            page += '<li class="paginate_button active"><a href="#">'+i+'</a></li>';
        }else{
            page += '<li class="paginate_button"><a class="num" href="javascript:;">'+i+'</a</li> ';
        }
    }

    //console.log(page);
    //下一页和尾页
    if(total == config.page){
        page += '<li class="paginate_button disabled"><span>下一页</span></li>';
    }
    if(total > config.page){
        page += '<li class="paginate_button"><a href="javascript:;" class="next">下一页</a></li>';
    }
    $('.count span').html(config.nums);
    $(pageClass).html(page);
    apage(total,list,apitype);
}

//分页页码显示
function apage(total,list,apitype){
    var page = $('.page .active').text();
    $('.page li a').click(function(){
        var type = $(this).attr('class');
        var pageNow;
        switch (type){
            case 'first':
            	pageNow = 1;
                break;
            case 'prev':
                pageNow = page-1;
                break;
            case 'next':
                pageNow = page-0+1;
                break;
            case 'last':
                pageNow = total;
                break;
            default:
                pageNow = $(this).html();
                break;
        }
        getList(list,apitype,pageNow);
    });
}