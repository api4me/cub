// JavaScript Document

$(document).ready(function() {
    //视频播放按钮
	$(".video-play").click(function(){
		$("i",this).toggle()
		});
		
	//比赛模式-线下列表
	$('#marquee5').kxbdSuperMarquee({
            isEqual: true,//所有滚动的元素长宽是否相等,true,fal
            distance:23,//一次滚动的距离
            time:2,//停顿时间，单位为秒
            direction: 'up'//direction: 'left',//滚动方向，'left','right','up','down'
        });
	//广告滚动固定
	if($('#fix-left').length!=0){
	   $("#fix-left").scrollFix({distanceTop:50});
	}
	if($('#fix-right').length!=0){
	   $("#fix-right").scrollFix({distanceTop:50});
	}
	
	
	//图片链接无间隙滚动
    function ScrollImgLeft() {
        var speed = 20
        var scroll_begin = document.getElementById("scroll_begin");
        var scroll_end = document.getElementById("scroll_end");
        var scroll_div = document.getElementById("scroll_div");
        scroll_end.innerHTML = scroll_begin.innerHTML
        function Marquee() {
            if (scroll_end.offsetWidth - scroll_div.scrollLeft <= 0)
                scroll_div.scrollLeft -= scroll_begin.offsetWidth
            else
                scroll_div.scrollLeft++
        }

        var MyMar = setInterval(Marquee, speed)
        scroll_div.onmouseover = function () {
            clearInterval(MyMar)
        }
        scroll_div.onmouseout = function () {
            MyMar = setInterval(Marquee, speed)
        }
    }
    ScrollImgLeft();

    $('.registration .form .submit a').click(function() {
        var f = '.registration .form ';
        var data = {
            'mobile': $.trim($(f + 'input[name="mobile"]').val()),
            'qq': $.trim($(f + 'input[name="qq"]').val()),
            'name': $.trim($(f + 'input[name="name"]').val()),
            'score': $.trim($(f + 'input[name="score"]').val()),
            'gmobile': $.trim($(f + 'input[name="gmobile"]').val()),
            'gqq': $.trim($(f + 'input[name="gqq"]').val()),
            'gname': $.trim($(f + 'input[name="gname"]').val()),
            'gteam': $.trim($(f + 'input[name="gteam"]').val())
        };

        var ism = function(str) {
            return /^1\d{10}$/.test(str);
        }
        var isn = function(str) {
            return /^[0-9]*$/.test(str);
        }

        if (is.empty(data.mobile) && is.empty(data.gmobile)) {
            alert('请输入手机号');
            return false;
        }

        if (!is.empty(data.mobile) && !ism(data.mobile)) {
            alert('请输入正确的手机号(个人报名)');
            return false;
        }
        if (!is.empty(data.score) && !isn(data.score)) {
            alert('请输入正确的天梯分数');
            return false;
        }

        if (!is.empty(data.gmobile) && !ism(data.gmobile)) {
            alert('请输入正确的手机号(组队报名)');
            return false;
        }

        $.post('/dota2/regist.php', {data: data}, function(resp) {
            alert(resp.msg);
            if (resp.status == 200) {
                $('.registration .form input[type="text"]').each(function() {
                    $(this).val('');
                });
            }
        }, 'json');

        return false;
    });

});
