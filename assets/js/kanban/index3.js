let agent = navigator.userAgent.toLowerCase();

$(".Thissortable2").draggable({
    connectToSortable: ".TodoWrapper2",
    appendTo: "#CanToDoStandbyArea2",
    scroll: false,
    helper:  "original",
    revert: "invalid"
});

$('#CanToDoStandbyArea2').sortable({
    revert: true
}).disableSelection();

$(document).on("click", ".btn_each_save_seq", function (e) {

    var targetidx =  $(this).data("idx");
    var thisparent = $(this).closest("span");
    let newArray = [];
    $('.ThisCanSorList').each(function(){
        newArray.push($(this).data('idx'));
    });

    if ( newArray.length > 0  ) {
        if (confirm("ToDo 순서를 저장하시겠습니까?")) {
            $.ajax({
                type: "POST",
                dataType: "json",
                url: "/manager/project/sequpdate",
                data: "SeqData=" + newArray + "&UserIdx=" + targetidx,
                async: false,
                beforeSend: function () {
                    $('.wrap-loading').removeClass('display_none');
                },
                success: function (json) {
                    if (json.result === true) {

                        /*$('#CanToDoStandbyArea2').addClass("display_none");
                        thisparent.find('.btn_each_save_seq').addClass("display_none");
                        thisparent.find('.btn_each_cancle_seq').addClass("display_none");
                        thisparent.find('.btn_each_change_seq').removeClass("display_none");*/
                        alert('변경되었습니다.');
                        location.reload();
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

        }
        return false;

    }
    e.preventDefault();
    return false;
});

$(document).on("click", ".btn_each_change_seq", function (e) {
    var targetidx = $(this).data("idx");
    fn_ajax_get_each_todo(targetidx);

    $('#CanToDoStandbyArea2').removeClass("display_none");
    $(this).addClass("display_none");
    $(this).closest("span").find('.btn_each_save_seq').removeClass("display_none");
    $(this).closest("span").find('.btn_each_cancle_seq').removeClass("display_none");
    e.preventDefault();
    return false;
});

$(document).on("click", ".btn_each_cancle_seq", function (e) {

    $('#CanToDoStandbyArea2').addClass("display_none");
    $(this).closest("span").find('.btn_each_save_seq').addClass("display_none");
    $(this).closest("span").find('.btn_each_cancle_seq').addClass("display_none");
    $(this).closest("span").find('.btn_each_change_seq').removeClass("display_none");
    e.preventDefault();
    return false;
});

function fn_ajax_get_each_todo(thisuser) {
    // 불러온다
    $.ajax({
        type: "POST",
        dataType: "json",
        url: "/manager/project/getMyTodoWorks",
        data: "UserIdx=" + thisuser,
        async: false,
        success: function (json) {

            if (json.totalCount > 0 ) {
                $("#CanToDoStandbyArea2").html('');

                let html = "";
                for(var i = 0; i < json.dataList.length; i++) {
                    let pname = json.dataList[i]['ProjectTitle'];
                    html = "<div class='kanban-item ThisCanSorList' style='cursor: move !important;' id='workid_"+json.dataList[i]['ProjectWorkIdx']+"' data-id='workid_"+json.dataList[i]['ProjectWorkIdx']+"' data-idx='"+json.dataList[i]['ProjectWorkIdx']+"'>";

                    //리스트에 추가
                    if ( json.dataList[i]['Priority'] == 9 ) {
                        html += "<span class='emergency'></span>";
                    }
                    html += "<br />프로젝트 : "+pname+"<span class='project_title_wrap'><i class='fa fa-info-circle noh_cursor btn_click_info text-light-blue' data-idx='"+json.dataList[i]['ProjectWorkIdx'] +"'></i><span class='TargetProjectName'>&nbsp;&nbsp;"+json.dataList[i]['title']+"</span></span></div>";
                    $("#CanToDoStandbyArea2").append(html);

                }


                return false;
            }

        }
    });
}


function fn_ajax_get_todo(xmode) {
    // 불러온다
    $.ajax({
        type: "POST",
        dataType: "json",
        url: "/manager/project/getReadyWorks",
        data: "mode=2",
        async: false,
        success: function (json) {
            if (json.totalCount > 0 ) {
                $("#btn_view_todo").data("idx",json.totalCount);
                if ( xmode == 'todo') {
                    $("#ToDoStandbyArea").html('');
                }else{
                    $("#CanToDoStandbyArea").html('');
                }
                let html = "";
                for(var i = 0; i < json.dataList.length; i++) {
                    let pname = json.dataList[i]['ProjectTitle'];

                    if ( xmode == 'todo') {
                        html = "<div class='kanban-item Thisdraggable nowReadyList' style='cursor: move !important;' id='workid_"+json.dataList[i]['ProjectWorkIdx']+"'  data-id='workid_"+json.dataList[i]['ProjectWorkIdx']+"' data-idx='"+json.dataList[i]['ProjectWorkIdx']+"'>";
                    }else{
                        html = "<div class='kanban-item Thissortable' style='cursor: move !important;' id='workid_"+json.dataList[i]['ProjectWorkIdx']+"' data-id='workid_"+json.dataList[i]['ProjectWorkIdx']+"' data-idx='"+json.dataList[i]['ProjectWorkIdx']+"'>";
                    }

                    //리스트에 추가
                    if ( json.dataList[i]['Priority'] == 9 ) {
                        html += "<span class='emergency'></span>";
                    }
                    html += "<br />프로젝트 : "+pname+"<span class='project_title_wrap'><i class='fa fa-info-circle noh_cursor btn_click_info text-light-blue' data-idx='"+json.dataList[i]['ProjectWorkIdx'] +"'></i><span class='TargetProjectName'>&nbsp;&nbsp;"+json.dataList[i]['title']+"</span></span></div>";
                    if ( xmode == 'todo') {
                        $("#ToDoStandbyArea").append(html);
                        $( "#workid_"+json.dataList[i]['ProjectWorkIdx']).draggable({
                            zIndex: 100002,
                            appendTo: 'body',
                            scroll: false,
                            helper:  "clone",
                            revert: "invalid"
                        });
                    }else{
                        $("#CanToDoStandbyArea").append(html);
                    }

                }
            }

        }
    });
}
$( function() {

    $(document).on("change", "#ProjectGroup", function (e) {
        // 불러온다
        $.ajax({
            type: "POST",
            dataType: "json",
            url: "/manager/project/getSubTeam",
            data: "GroupCode=" + $(this).val(),
            async: false,
            success: function (json) {
                if (json.totalCount > 0 ) {
                    let valProjectTeam = $("#ProjectTeam").val();
                    $("#ProjectTeam").html('');
                    let html = "<option value='0'>팀선택</option>";
                    $("#ProjectTeam").append(html);
                    for(var i = 0; i < json.dataList.length; i++) {
                        let is_selected = '';
                        if ( valProjectTeam == json.dataList[i]['IDX'] ) {
                            is_selected = 'selected';
                        }
                        html = "<option value='"+json.dataList[i]['IDX']+"' "+ is_selected + ">"+json.dataList[i]['NAME']+"</option>";
                        $("#ProjectTeam").append(html);
                    }

                    return false;
                }

            }
        });

    });

    $(document).on("click", "#btn_view_todo", function (e) {
        if ( $(this).hasClass('btn-primary')) {
            $('#ToDoStandbyArea').animate({
                "margin-left":"-300"
            },500,function(){
            });
            $(this).removeClass('btn-primary');
            $(this).addClass('btn-default');
            var nowReadycnt = $("#btn_view_todo").data("idx");
            $(this).text("대기업무("+ nowReadycnt + "개)열기");
            $("#btn_add_todo").addClass("display_none");
            $("#btn_change_seq").addClass("display_none");
            $("#btn_set_todo").addClass("display_none");
            $(".btn_each_change_seq").removeClass("display_none");

        }else{
            $('#ToDoStandbyArea').animate({
                "margin-left":"300"
            },500,function(){
            });
            $(this).removeClass('btn-default');
            $(this).addClass('btn-primary');
            $(this).text('대기업무닫기');
            fn_ajax_get_todo('todo');
            $("#btn_add_todo").removeClass("display_none");
            $("#btn_change_seq").removeClass("display_none");
            $("#btn_set_todo").removeClass("display_none");
            $(".btn_each_change_seq").addClass("display_none");
        }
        e.preventDefault();
        return false;
    });

    $(document).on("click", "#btn_change_seq", function (e) {
        $('#ToDoStandbyArea').animate({
            "margin-left":"-300"
        },500,function(){
        });

        fn_ajax_get_todo('sort');

        $('#CanToDoStandbyArea').removeClass("display_none");
        $(this).addClass("display_none");
        $('#btn_save_seq').removeClass("display_none");
        $('#btn_cancle_seq').removeClass("display_none");
        $('#btn_set_todo').addClass("display_none");
        $('#btn_add_todo').addClass("display_none");
        $('#btn_view_todo').addClass("display_none");

        e.preventDefault();
        return false;
    });
    $(document).on("click", "#btn_cancle_seq", function (e) {
        $('#ToDoStandbyArea').animate({
            "margin-left":"300"
        },500,function(){
        });

        $('#CanToDoStandbyArea').addClass("display_none");
        $('#btn_save_seq').addClass("display_none");
        $('#btn_cancle_seq').addClass("display_none");
        $('#btn_view_todo').removeClass("display_none");
        $('#btn_add_todo').removeClass("display_none");
        $('#btn_change_seq').removeClass("display_none");
        e.preventDefault();
        return false;
    });



    $(document).on("click", "#btn_save_seq", function (e) {

        let newArray = [];
        $('.Thissortable').each(function(){
            newArray.push($(this).data('idx'));
        });

        if ( newArray.length > 0  ) {
            if (confirm("대기업무 정렬 순서를 저장하시겠습니까?")) {
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "/manager/project/sequpdate",
                    data: "SeqData=" + newArray,
                    async: false,
                    beforeSend: function () {
                        $('.wrap-loading').removeClass('display_none');
                    },
                    success: function (json) {
                        if (json.result === true) {
                            //location.reload();
                            fn_ajax_get_todo('todo');
                            $("#CanToDoStandbyArea").html('');
                            $('#CanToDoStandbyArea').addClass("display_none");
                            $('#btn_save_seq').addClass("display_none");
                            $('#btn_add_todo').addClass("display_none");
                            $('#btn_change_seq').addClass("display_none");
                            $('#btn_cancle_seq').addClass("display_none");
                            var nowReadycnt = $("#btn_view_todo").data("idx");
                            $('#btn_view_todo').removeClass('btn-primary').addClass('btn-default').removeClass("display_none").text("대기업무("+ nowReadycnt + "개)열기");
                            $('#btn_add_todo').addClass("display_none");
                            $('#btn_change_seq').addClass("display_none");
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

            }
            return false;

        }
        e.preventDefault();
        return false;
    });

    $(".Thisdraggable").draggable({
        zIndex: 100002,
        appendTo: 'body',
        scroll: false,
        helper:  "clone",//"original",
        revert: "invalid"
    });


    $(".Thissortable").draggable({
        connectToSortable: ".TodoWrapper2",
        appendTo: "#CanToDoStandbyArea",
        scroll: false,
        helper:  "original",
        revert: "invalid"
    });

    $('#CanToDoStandbyArea').sortable({
        revert: true
        /*connectWith: ".TodoWrapper2",
        items: "> div",
        forcePlaceholderSize: false,
        helper: function (e, div) {
            copyHelper = div.clone().insertAfter(div);
            return div.clone();
        },
        stop: function () {
            copyHelper && copyHelper.remove();
        }*/
    }).disableSelection();

    /*$("#CanToDoStandbyArea").sortable({
        receive: function (e, ui) {
            ui.helper.remove();
        }
    });*/

    $( ".thisdroppable" ).droppable({
        drop: function( event, dragui ) {
            let targetId = $(this).attr('id'); //userid
            let originId = dragui.draggable.closest("main").attr("id");
            let originId2 = dragui.draggable.data("parent");
            if ( originId === undefined ) {
                if ( originId2 === undefined ) {
                    originId = 'undefined';
                }else{
                    originId = originId2;
                }
            }
            if(targetId == "CanToDoStandbyArea" && originId == "undefined" ){
                var originid = $(dragui.draggable).attr("id");
                $("#" + originid).draggable('disable');
                return false;
            }

            /*if ( typeof dragui.draggable.attr("id") == "undefined" ) {
                return false;
            }else {
                let tmptargetworkidx = dragui.draggable.attr("id").split("_");
                let targetworkidx = tmptargetworkidx[1];
            }*/
            //console.log("dragui.draggable",dragui.draggable);
            let tmptargetworkidx = dragui.draggable.data("id").split("_");
            let targetworkidx = tmptargetworkidx[1];

            if(originId == "undefined" && targetId =='ToDoStandbyArea'){
                //$(this).draggable("destroy");
                return false;
            }
            dragui.draggable.css("inset",'');
            if (agent.indexOf("firefox") != -1) {
                dragui.draggable.css("position",'relative');
            }else{
                dragui.draggable.css("position",'');
            }
            dragui.draggable.css("width","100%");
            dragui.draggable.css("max-width","300px");
            dragui.draggable.css("z-index",100001);
            dragui.draggable.detach().appendTo($(this));

            if ( targetId == 'ToDoStandbyArea') { //업무초기화
                fn_todoset(targetworkidx,'CLEAR',originId);
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
    var nowReadycnt = $("#btn_view_todo").data("idx");
    var newReadycnt = nowReadycnt-1;
    $("#btn_view_todo").data("idx",newReadycnt);
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
                var newcnt = $("#ToDoStandbyArea").find(".nowReadyList").length;
                $("#btn_view_todo").data("idx",newcnt);
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
        let gidx = $("#ProjectGroup").val();
        if ( idx > 0 ) {
            location.href='/manager/kanban/' + idx +'/' + gidx;
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
                    let html = "<div class='kanban-item Thisdraggable' style='cursor: move !important;' data-id='workid_"+res.result_idx+"' id='workid_"+res.result_idx+"' data-idx='"+res.result_idx+"'>";
                    //리스트에 추가
                    if ( res.Priority == 9 ) {
                        html += "<span class='emergency'></span>";
                    }
                    html += "<br >프로젝트 : "+pname+"<span class='project_title_wrap'><i class='fa fa-info-circle noh_cursor btn_click_info text-light-blue' data-idx='"+res.result_idx +"'></i><span class='TargetProjectName'>"+$('#title').val()+"</span></span></div>";
                    $("#ToDoStandbyArea").append(html);
                    $( "#workid_"+res.result_idx).draggable({
                        zIndex: 100002,
                        appendTo: 'body',
                        scroll: false,
                        helper:  "clone",
                        revert: "invalid"
                    });
                    $('#formreg_todo').hide();

                    var nowReadycnt = $("#btn_view_todo").data("idx");
                    var newReadycnt = nowReadycnt +1;
                    $("#btn_view_todo").data("idx",newReadycnt);
                    $("#btn_view_todo").text("대기업무("+ newReadycnt + "개)열기");
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