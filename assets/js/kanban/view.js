
$(document).ready(function(){

    let LoadAreaWidth = parseInt($("#TeamProjectArea").width());
    const LoadMemberCount = $(".kanban-container").length;

    if (LoadAreaWidth > LoadMemberCount*300 ) {
        $(".thisSliderBtn").addClass("display_none");
    }


    var doubleSubmitFlag = false;
    function doubleSubmitCheck(){
        if(doubleSubmitFlag){

            return doubleSubmitFlag;
        }else{

            doubleSubmitFlag = true;
            return false;
        }
    }
    var doubleSubmitFlag2 = false;
    function doubleSubmitCheck2(){
        if(doubleSubmitFlag2){

            return doubleSubmitFlag2;
        }else{

            doubleSubmitFlag2 = true;
            return false;
        }
    }

    $(document).keyup(function(event) {
        if(doubleSubmitCheck()) return;

        if (event.keyCode == '37') {
            $("#prevBtn").trigger("click");
        }
        else if (event.keyCode == '39') {
            $("#nextBtn").trigger("click");
        }

        return false;
    });


    function tourLandingScript() {
        let TargetAreaWidth = parseInt($("#TeamProjectArea" ).width());
        if (TargetAreaWidth > LoadMemberCount*300 ) {
            $(".thisSliderBtn").addClass("display_none");
        }else{
            $(".thisSliderBtn").removeClass("display_none");
        }

        let viewCount = parseInt(TargetAreaWidth/300);
        KanbanSlider.reloadSlider({
            autoControls: true,
            speed: 500,
            slideSelector: 'li',
            minSlides: 1,
            maxSlides: viewCount,
            moveSlides: 1,
            slideWidth: 300,
            slideMargin: 5,
            pager:false,
            controls:false,
            infiniteLoop:false,
            touchEnabled:false,
            oneToOneTouch: false,
            onSliderLoad: function () {
                $(".bx-viewport").css("overflow","");
            },
        });
    }

    $(window).resize(function(){
        tourLandingScript();
    });

    let TargetAreaWidth = parseInt($("#TeamProjectArea" ).width());
    let viewCount = parseInt(TargetAreaWidth/300);
    let KanbanSlider = $('#teamKanban').bxSlider({
        autoControls: true,
        speed: 500,
        slideSelector: 'li',
        minSlides: 1,
        maxSlides: viewCount,
        moveSlides: 3,
        slideWidth: 300,
        slideMargin: 5,
        pager:false,
        controls:false,
        infiniteLoop:false,
        touchEnabled:false,
        oneToOneTouch: false,
        onSliderLoad: function () {
            $(".bx-viewport").css("overflow","");
        },

    });



    $("#prevBtn").click(function(e){
        if(doubleSubmitCheck2()) return;
        KanbanSlider.goToPrevSlide();
        setTimeout(function() {
            doubleSubmitFlag = false;
            doubleSubmitFlag2 = false;
        }, 500);
        e.preventDefault();
        return false;
    });

    $("#nextBtn").click(function(e){
        if(doubleSubmitCheck2()) return;
        KanbanSlider.goToNextSlide();
        setTimeout(function() {
            doubleSubmitFlag = false;
            doubleSubmitFlag2 = false;
        }, 500);
        e.preventDefault();
        return false;
    });


    $(document).on("click", ".popcls", function (e) {
        $('#formreg_todo').hide();
        e.preventDefault();
        return false;
    });

    $(document).on("click", ".btn_click_info", function () {
        let idx = $(this).data('idx');
        $("#popdetail").setLayer({
            'url' : '/manager/project/popdetail2/' + idx,
            'width' : 1024,
            'max_height' : 500
        });
    });

    $(document).on("click", ".intra_page_link", function () {
        let go_url = $(this).data('url');
        window.open(go_url, '_blank');
        return false;
    });

    $(document).off('change', '#ProjectTeam').on('change', '#ProjectTeam',function() {
        let idx = $(this).val();
        if ( idx > 0 ) {
            location.href='/manager/monitor/' + idx ;
        }
        return false;
    });

    $(document).on("click", ".top_arrow", function () {
        $( 'html, body' ).animate( { scrollTop : 0 }, 1000 );
        return false;
    });

    $(document).scroll(function() {
        var scrolly = $(this).scrollTop();
        var scrolly2 = scrolly-100;
        if (scrolly > 160) {
            $('.top_arrow').removeClass("display_none");
            $('.teamKanban2_child').removeClass("display_none");
            $('.teamKanban2_child').css("top",scrolly2);
        } else {
            $('.top_arrow').addClass("display_none");
            $('.teamKanban2_child').css("top",0);
            $('.teamKanban2_child').addClass("display_none");
        }
    });



});