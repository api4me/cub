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

});
