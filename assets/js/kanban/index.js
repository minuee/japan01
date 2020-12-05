let agent = navigator.userAgent.toLowerCase();
$( function() {

    $( ".Thisdraggable" ).draggable({
        zIndex: 10,
        helper : "clone",
        revert: 'invalid',
    });
    $( ".thisdroppable" ).droppable({
        drop: function( event, dragui ) {
            let targetId = $(this).attr('id'); //userid
            let originId = dragui.draggable.closest("main").attr("id");
            if ( originId === undefined ) originId = 'undefined';
            let tmptargetworkidx = dragui.draggable.attr("id").split("_");
            let targetworkidx =  tmptargetworkidx[1];

            if(originId == "undefined" && targetId =='ToDoStandbyArea'){
                //$(this).draggable("destroy");
                return false;
            }
            /*ui.draggable.removeClass("ui-draggable");
            ui.draggable.removeClass("ui-draggable-handle");
            ui.draggable.css("inset",'');
            ui.draggable.css("position",'');
            ui.draggable.css("z-index",'100');
            ui.draggable.detach().appendTo($(this));*/

            //ui.draggable.removeAttr("style");
            //ui.draggable.removeClass("ui-draggable");
            //ui.draggable.removeClass("ui-draggable-handle");
            dragui.draggable.css("inset",'');
            if (agent.indexOf("firefox") != -1) {
                dragui.draggable.css("position",'relative');
            }else{
                dragui.draggable.css("position",'');
            }
            dragui.draggable.css("width","100%");
            dragui.draggable.css("z-index",100);
            dragui.draggable.detach().appendTo($(this));
            //ui-draggable ui-draggable-handle

            if ( targetId == 'ToDoStandbyArea') { //업무초기화
                //if ( targetId !== originId ) {
                    fn_todoset(targetworkidx,'CLEAR',originId);
                //}

            }else{ //업무할당
                if ( targetId !== originId ) {
                   fn_todoset(targetworkidx,targetId,originId);
                }
            }


        }
    });
} );


function  removeitem(targetIdx) {
    $('#workid_'+ targetIdx ).remove();
    return false;
}
function ajax_statics_update( ) {

}

function fn_todoset(targetworkidx,targetId,OriginToDoID){

    let TargetProjectName =  $("#workid_" + targetworkidx).find(".TargetProjectName").text();
    TargetProjectName = TargetProjectName.replace("☞ 인트라넷 바로가기","");
    jQuery.ajax({
        type: "POST",
        dataType: "json",
        url: "/manager/project/settodo",
        data: "ProjectWorkIdx=" + targetworkidx + "&ToDoID=" + targetId +"&OriginToDoID="+ OriginToDoID,
        async: false,
        beforeSend: function () {
            $('.wrap-loading').removeClass('display_none');
        },
        success: function (json) {
            if (json.result === false) {
                alert('처리중 오류가 발생하였습니다');
                return false;
            } else {
                // 해당 팀페이지에 알림 전송
                let newArray = [];
                let makedata = {};
                let messenger_user = "";
                if ( targetId == "CLEAR" ) {
                    //messenger_user = json.ToDoUser+"의 업무가 취소";
                    messenger_user = "[알림]업무취소 담당자:" + json.ToDoUser + ",업무명:" + TargetProjectName;
                }else if ( OriginToDoID == "undefined"  && targetId > 0 ) {
                    //messenger_user = json.ToDoUser+"의 신규업무할당";
                    messenger_user = "[알림]신규업무 담당자:" + json.ToDoUser + ",업무명:" + TargetProjectName;
                }else{
                    //messenger_user = json.OldToDoUser+"의 업무가 "+ json.ToDoUser +"으로 변경";
                    messenger_user = "[알림]담당변경 :" + json.OldToDoUser + " ▶ " + json.ToDoUser + ",업무명:" + TargetProjectName;
                }
                makedata.cal_UID = $("#UID").val();
                makedata.cal_title = "프로젝트 업무 할당";
                makedata.cal_start = null;//res.data.TmpSdate;
                makedata.cal_end = null;//res.data.TmpSdate;
                makedata.cal_id = targetworkidx;
                makedata.cal_todouser = messenger_user;
                newArray.push(makedata);
                ui.display.addIndivisualTodo(newArray);

                //메시지를 채팅창에 넣어준다
                ui.message.notifysend(messenger_user);
                $('.wrap-loading').addClass('display_none');
                return false;

            }
        },
        complete: function () {
            $('.wrap-loading').addClass('display_none');
        }
    });
}




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

    $(document).on("click", "#btn_add_todo", function (e) {
        $('#formreg_todo').show();
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
            location.href='/manager/kanban/' + idx;
        }
        return false;
    });


    $('#TodoRegFrom').on("submit", function() {

        if ( $('#ProjectIdx').val() == "") {
            alert("프로젝트 선택은 필수입니다..");
            $('#CompanyRegistrationNo').focus();
            return false;
        }
        if ( $('#title').val() == "") {
            alert("업무타이틀을 입력해주세요");
            $('#CompanyRegistrationNo').focus();
            return false;
        }
        if ( $('#Foretime').val() < 1) {
            alert("예상소요시간을 입력해주세요");
            $('#Foretime').focus();
            return false;
        }

        if (!confirm('Todo업무를 등록하시겠습니까?')) return false;

        $.ajax({
            type: "POST",
            url: "/manager/project/addtodo",
            data: $('#TodoRegFrom').serialize(),
            dataType: "JSON",
            async : false,
            success: function(res){
                if ( res.result === false ) {
                    alert(res.message);
                    return false;
                }else{
                    let pname = $("#ProjectIdx option:selected").text();
                    //리스트에 추가
                    html = "<div class='kanban-item Thisdraggable' style='cursor: pointer !important;' id='workid_"+res.result_idx+"'>프로젝트 : "+pname+"<br ><span class='project_title_wrap'><i class='fa fa-info-circle noh_cursor btn_click_info' data-idx='"+res.result_idx +"'></i><span class='TargetProjectName'>"+$('#title').val()+"</span></span></div>";
                    $("#ToDoStandbyArea").append(html);
                    $( "#workid_"+res.result_idx).draggable({
                        zIndex: 10,
                        helper : "clone",
                        revert: 'invalid',
                    });

                    $('#formreg_todo').hide();
                }
            }
        });
        return false;
    });


    $(document).on("click", ".noti_close", function () {
        let strNoticeIdx = $(this).data('idx')?$(this).data('idx'):("#NowNoticeIdx").val();

        if (!confirm('공지를 내리시겠습니까?')) return false;
        jQuery.ajax({
            type: "POST",
            dataType: "json",
            url: "/manager/project/noticedelete",
            data: "NoticeIdx=" + strNoticeIdx,
            async: false,
            beforeSend: function () {
                $('.wrap-loading').removeClass('display_none');
            },
            success: function (json) {
                if (json.result === true) {

                    // 해당 팀페이지에 알림 전송
                    let newArray = [];
                    let makedata = {};
                    makedata.target_idx = strNoticeIdx;
                    makedata.target_title = $('#TargetNoticeTitle').text();
                    makedata.target_mode = 'remove';
                    newArray.push(makedata);
                    ui.display.fn_updateNotice(newArray);
                    $(".alarm_noti").addClass("display_none");
                    $("#NowNoticeIdx").val('');
                    return false;
                } else {
                    alert(json.message);
                    return false;

                }
            },
            complete: function () {
                $('.wrap-loading').addClass('display_none');
            }
        });

    });


});