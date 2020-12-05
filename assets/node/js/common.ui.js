$(function(){

	$('input, textarea').placeholder();

	//채팅 컨트롤
	$(".chat_head .icon_chat, .room_top .room_close").click(function(){
		var imgUrl = "images/"
		var el = $(".chat_wrap");
		//var thiscode = $(this).data('code');
		el.toggleClass("active");

		if(el.hasClass("active")){
			//$(".chat_head h2").find("img").attr("src", imgUrl + "chat_logo" + thiscode + "_on.png");
			$(".chat_head .icon_chat").find("img").attr("src", imgUrl + "chat_menu03_on.png");
			$(".chat_con").css('padding-right', '0');
			$(".tv_receive").css('height', '730px');
			$(".chat_con .r_con").hide();
		} else {
			//$(".chat_head h2").find("img").attr("src", imgUrl + "chat_logo"+ thiscode +".png");
			$(".chat_head .icon_chat").find("img").attr("src", imgUrl + "chat_menu03.png");
			$(".chat_con").css('padding-right', '418px');
			$(".tv_receive").css('height', '499px');
			$(".chat_con .r_con").show();
		}
	});

	//방송 정보
	$(".chat_txt_info .btn_info").click(function(){
		var rommH =""

		$(this).toggleClass("active");
		if($(this).hasClass("active")){

			$(".chat_txt_view").show();
			//챗 높이
			$(".room_box .chatScrollH").css("height", "660px");

			//퀵메뉴 위치
			$(".room_box .downScroll").css("top","700px");

		} else {
			$(".chat_txt_view").hide();
			$(".room_box .chatScrollH").css("height", "520px");
			$(".room_box .downScroll").css("top","577px");
		}
	});

	//채팅창 스크롤
	$(".chatScrollH").mCustomScrollbar({
	   axis:"y",
	   theme:"dark-3",
	   mouseWheelPixels: 500,
	   callbacks:{
			whileScrolling:function(){
				//채팅창 DOWN 추가
				myCustomFn(this);
			}
		}
   });

	//채팅창 로드시 맨 아래로 이동
	$(".chatScrollH").mCustomScrollbar("scrollTo","bottom",{
		scrollInertia:0
	});

	//방송정보 스크롤
	$(".chatScrollView").mCustomScrollbar({
	   axis:"y",
	   theme:"my-theme-gray",
	   mouseWheelPixels: 500
   });


	//방송정보 스크롤
	$(".textScrollH").mCustomScrollbar({
	   axis:"y",
	   theme:"dark-3",
	   mouseWheelPixels: 500
   });

	$(".room_box .downScroll").click(function(){
		$(".chatScrollH").mCustomScrollbar("scrollTo","bottom",{
			scrollInertia:500
		});
	});


	//input type Checkbox
	$('.check-area').children('input[type="checkbox"]').parent().each(function(index){
		if($('.check-area').children('input[type="checkbox"]').parent().eq(index).children('input[type="checkbox"]').is(':checked')){
			$('.check-area').children('input[type="checkbox"]').parent().eq(index).addClass('active');
		}
	});

	$('.check-area').children('input[type="checkbox"]').click(function(){
		var $this = $(this);
		if($this.is(':checked')){
			$this.parent().addClass('active');
		} else {
			$this.parent().removeClass('active');
		}
	});

	//textarea 스크롤
	checkAgree();

	//채팅 공지 높이 계산
	alamBox();
});

// 채팅 공지 높이 계산
var alamBox = function(){
	var alamNoti = $(".room_box .alarm_noti").height();
	var viewDisplay = $('.room_box .alarm_noti').css("display");
	if(viewDisplay == "none"){
		$('.room_box .chatScrollH').css({'height':'520'});
		$('.room_box').css({	'padding-top':'0'});

	} else  {
		$('.room_box .chatScrollH').css({'height':'520' - alamNoti});
		$('.room_box').css({	'padding-top':alamNoti});
	}
}


var checkAgree = function(){
	var checkAgreeTit = $(".check_agree dt a");
	var checkAgreeBox = $(".check_agree dd");

	checkAgreeTit.click(function(){
		var $this = $(this);
		var viewDisplay = $this.parent().next("dd").css("display");
		if(viewDisplay == "none"){
			checkAgreeBox.hide();
			checkAgreeTit.parent().removeClass("active");
			$this.parent().addClass("active").next().show();
		} else  {
			checkAgreeTit.parent().removeClass("active");
			checkAgreeBox.hide();
		}
	});

}

//채팅창 DOWN 컨트롤
function myCustomFn(el){
	var endSch = el.mcs.topPct;

	if(el.mcs.topPct != "100"){
		$(".room_box .downScroll").show();

	} else if(el.mcs.topPct == "100") {
		$(".room_box .downScroll").hide();
	}
}

//레이어 팝업
function layerOpen(IdOpen){
	//화면의 높이와 너비를 구한다.
	var maskWidth = $(window).width();
	var maskHeight = $(window).height();

	$('body').addClass("no-scroll");

	$('.dim-layer').css({'width':maskWidth,'height':maskHeight});
	$(IdOpen).before('<div class="dim-layer"></div>');
	$(IdOpen).show();
};

//레이어 팝업 닫기
function layerClose(IdClose){
	$('body').removeClass("no-scroll");
	$(".live_control li").removeClass("active");
	$(IdClose).hide();
	$(".dim-layer").remove();

}
