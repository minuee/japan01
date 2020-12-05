$(function(){

	//방송 컨트롤
	$(".live_control li").click(function(){
		$(".live_control li").removeClass("active");
		$(this).addClass("active");
	});

	//채팅 레이어 메뉴 펼치기
	$(".clickLayerMenu").click(function(e){
		var _posT = $(this).offset().top;

		$('body').addClass("no-scroll");
		$(".layer-area-mn").show();

		$(".layer-area-mn").css('top', _posT - '240');

	});

	//클릭 자신 제외
	$(document).click(function(e){
		if (!$(e.target).is('.clickLayerMenu')) {
			$(".layer-area-mn").hide();
			$('body').removeClass("no-scroll");
		}
	});


});



//레이어 팝업
function layerOpen(IdOpen){
	//화면의 높이와 너비를 구한다.
	var maskWidth = $(window).width();
	var maskHeight = $(window).height();

	$(".dim-layer").remove();
	$(".layer-area-mn").hide();
	$('body').addClass("no-scroll");
	$('.dim-layer').css({'width':maskWidth,'height':maskHeight});

	$("#"+IdOpen).before('<div class="dim-layer"></div>');
	$("#"+IdOpen).show();
	$("#"+IdOpen).click(function(){
		$(this).hide();
		$('.dim-layer').hide();
		$('body').removeClass("no-scroll");
	});
};

//레이어 팝업 닫기
function layerClose(IdClose){
	$('body').removeClass("no-scroll");
	$(".live_control li").removeClass("active");
	$("#" + IdClose).hide();
	$(".layer-area-mn").hide();
	$(".dim-layer").remove();

}
