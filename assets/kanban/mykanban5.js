function fn_ajax_get_todo(mode) {


    // 불러온다
    $.ajax({
        type: "POST",
        dataType: "json",
        url: "/manager/project/getMyTodoWorks",
        data: "UserIdx=" + null,
        async: false,
        success: function (json) {
            if (json.totalCount > 0 ) {
                $("#CanToDoStandbyArea").html('');

                let html = "";
                for(var i = 0; i < json.dataList.length; i++) {
                    let pname = json.dataList[i]['ProjectTitle'];
                    html = "<div class='kanban-item Thissortable' style='cursor: move !important;' id='workid_"+json.dataList[i]['ProjectWorkIdx']+"' data-id='workid_"+json.dataList[i]['ProjectWorkIdx']+"' data-idx='"+json.dataList[i]['ProjectWorkIdx']+"'>";

                    //리스트에 추가
                    if ( json.dataList[i]['Priority'] == 9 ) {
                        html += "<span class='emergency'></span>";
                    }
                    html += "<br />프로젝트 : "+pname+"<span class='project_title_wrap'><i class='fa fa-info-circle noh_cursor btn_click_info' data-idx='"+json.dataList[i]['ProjectWorkIdx'] +"'></i><span class='TargetProjectName'>&nbsp;&nbsp;"+json.dataList[i]['title']+"</span></span></div>";
                    $("#CanToDoStandbyArea").append(html);

                }

                if ( mode == "todo") {
                    location.reload();
                }

                $(".Thissortable").draggable({
                    connectToSortable: ".TodoWrapper2",
                    appendTo: "#CanToDoStandbyArea",
                    scroll: false,
                    helper:  "original",
                    revert: "invalid"
                });

                $('#CanToDoStandbyArea').sortable({
                    revert: true
                }).disableSelection();
                return false;
            }

        }
    });
}

$(function () {

    //레포트 미제출 체크 readyAlert
    if ( $("#GlobalReportOk").val() ==  0 ) {
        readyAlert();
    }


    $(document).on("click", "#btn_save_seq", function (e) {

        let newArray = [];
        $('.Thissortable').each(function(){
            newArray.push($(this).data('idx'));
        });

        if ( newArray.length > 0  ) {
            if (confirm("ToDo 노출순서를 저장하시겠습니까?")) {
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
                            fn_ajax_get_todo('todo');
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

    $(document).on("click", "#btn_change_seq", function (e) {
        fn_ajax_get_todo('load');

        $('#CanToDoStandbyArea').removeClass("display_none");
        $(this).addClass("display_none");
        $('#btn_save_seq').removeClass("display_none");
        $('#btn_cancle_seq').removeClass("display_none");
        $('#addToDo').addClass("display_none");

        e.preventDefault();
        return false;
    });

    $(document).on("click", "#btn_cancle_seq", function (e) {

        $('#CanToDoStandbyArea').addClass("display_none");
        $('#btn_save_seq').addClass("display_none");
        $('#btn_cancle_seq').addClass("display_none");
        $('#addToDo').removeClass("display_none");
        $('#btn_change_seq').removeClass("display_none");
        e.preventDefault();
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
                    res.Priority == 9 ? isemergency = "<span class='emergency'></span>" : isemergency = "";
                    KanbanTest.addElement("_todo", {
                        id: res.result_idx,
                        title: isemergency + "["+pname+"]<br /><i class='fa fa-info-circle noh_cursor btn_click_info text-light-blue' data-idx='"+ res.result_idx +"'></i>&nbsp;&nbsp;<b>" + $('#title').val() + "<input type='hidden' id='ChildMode_"+ res.result_idx +"' value='"+ res.ChildMode + "'><input type='hidden' id='Foretime_ "+res.result_idx + "' value='"+res.Foretime +"'></b>",
                        drag: function(el, source) {
                            fn_checkdoing(source.parentNode.dataset.id);
                            return false;
                        },
                        dragend: function(el) {
                            //console.log("END DRAG: " + el.dataset.eid);
                            return false;
                        },
                        drop: function(el,source) {
                            //console.log("END drop: " + el.dataset.eid);
                            fn_dropaction(source.parentNode.dataset.id);
                            fn_todo_update(el.dataset.eid);

                            return false;
                        }
                    });
                    $('#title').val('');
                    $('#Foretime').val(10);
                    $("#ProjectIdx").find('option:eq(0)').prop('selected', true);
                    $("#Priority").find('option:eq(0)').prop('selected', true);
                    $("#ChildMode").find('option:eq(0)').prop('selected', true);
                    $('#formreg_todo').hide();
                    ajax_statics_update(null,null,null);


                }
            }
        });
        return false;
    });
});

function fn_move_myall(){
    let thisidx = $("#UID").val();
    location.href = "/manager/project/myview/" + thisidx;
    return false;
}

function refresh_intrainfo( modee)
{

    if (!confirm('데이터를 수동으로 갱신하시겠습니까??')) return false;
    let sendurl = '/api/getintra' + modee;


    jQuery.ajax({
        type: "POST",
        dataType: "json",
        url: sendurl,
        data: "UserID=" + $("#UID").val(),
        async: false,
        beforeSend: function () {
            $('.wrap-loading').removeClass('display_none');
        },
        success: function (json) {
            if (json.result === true) {
                alert('정상적으로 등록되었습니다.');
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

function fn_todayreport(num){

    if ( $("#NowDoingCount").val() > 0 ) {
        alert("Doing 진행중인 업무가 있을경우 업무리포트를 생성할 수 없습니다.\r\n  ToDo로 옮긴이후 명일이후 Doing으로 옮겨 진행해주세요");
        return false;
    }

    var Target_My_doing_Count =  parseInt($("#Target_My_doing_Count").text());
    var Target_My_done_Count =  parseInt($("#Target_My_done_Count").text());
    if (Target_My_doing_Count == 0 && Target_My_done_Count == 0 ){
        alert("대기중이거나 완료된 업무가 하나도 없어 생성이 불가합니다.");
        return false;
    }

    if ( num > 0 ) {
        if (!confirm('금일 작성된 업무리포트가 있습니다 \r\n 추가 작성하시겠습니까?')) return false;
    }else{
        if (!confirm('업무리포트 작성하시겠습니까?')) return false;
    }


    jQuery.ajax({
        type: "POST",
        dataType: "json",
        url: "/manager/report/todayinsert",
        data: "UserID=" + $("#UID").val(),
        async: false,
        beforeSend: function () {
            $('.wrap-loading').removeClass('display_none');
        },
        success: function (json) {
            if (json.result === true) {
                alert('정상적으로 등록되었습니다.');
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
function formatNumber(n) {
    return n.replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
}

function ajax_statics_update(targetIdx ,xChildMode  , xForetime  , xTargetBackground ) {
    if ( targetIdx != null ) {
        $("#Foretime_" + targetIdx).val(xChildMode);
        $("#ChildMode_" + targetIdx).val(xForetime);
        if ( xTargetBackground ) {
            $("#Background_" + targetIdx).removeClass('bg-post-default bg-post-green bg-post-skyblue bg-post-pink bg-post-red');
            $("#Background_" + targetIdx).addClass(xTargetBackground);
        }else{
            $("#Background_" + targetIdx).removeClass('bg-post-green bg-post-skyblue bg-post-pink bg-post-red');
            $("#Background_" + targetIdx).addClass('bg-post-default');
        }

    }

    if ( $("#UID").val() ) {
        $.ajax({
            type        : 'POST' ,
            async       : false,
            url         : "/manager/project/myjobdate",
            data        : "USERID="+ $("#UID").val(),
            dataType    : 'JSON',
            success  : function(res) {
                if ( res.result !== true ) {
                    return false;
                }

                if ( res.TODOSTATICS.SUMCount > 0 ) {
                    $("#Target_My_doing_Count").text(res.TODOSTATICS.SUMCount);
                    $("#Target_My_doing_Count2").text(res.TODOSTATICS.SUMEmergency);
                    $("#Target_My_doing_Count3").text(res.TODOSTATICS.SUMReopen);
                    $("#Target_My_doing_Count4").text('');
                    if ( res.TODOSTATICS.SUMDForetime>60 ) {
                        let SUMDForetime2 = res.TODOSTATICS.SUMDForetime/60;
                        let insertstr =  formatNumber(res.TODOSTATICS.SUMDForetime) +'분 ( ' + SUMDForetime2.toFixed(1) + ' 시간 )';
                        $("#Target_My_doing_Count4").text(insertstr) ;
                    } else{
                        if ( res.TODOSTATICS.SUMDForetime ===  null ) res.TODOSTATICS.SUMDForetime = 0;
                        $("#Target_My_doing_Count4").text(res.TODOSTATICS.SUMDForetime+'분');
                    }

                }
                if ( res.DONESTATICS.SUMCount > 0 ) {
                    $("#Target_My_done_Count").text(res.DONESTATICS.SUMCount);
                    $("#Target_My_done_Count2").text('');
                    if ( res.DONESTATICS.SUMDoingTime>60 ) {
                        let SUMDoingTime2 = res.DONESTATICS.SUMDoingTime/60;
                        let insertstr2 =formatNumber(res.DONESTATICS.SUMDoingTime)+'분 ( ' + SUMDoingTime2.toFixed(1) + ' 시간 )';
                        $("#Target_My_done_Count2").text(insertstr2);
                    } else{
                        if ( res.DONESTATICS.SUMDoingTime ===  null ) res.TODOSTATICS.SUMDoingTime = 0;
                        $("#Target_My_done_Count2").text(res.DONESTATICS.SUMDoingTime+'분');
                    }
                }
                return false;
            },
            error : function (ts) {
                alert(ts.responseStatus);
            }
        });
    }


}

function ajax_todo_udpate(mode, widx){

    let wstatus  = 0;
    if (mode == 'doing' || mode == 'return' ) {
        wstatus = 2;
    }else if (mode == 'done' ) {
        wstatus = 9;
    }else{
        wstatus  = 1;
    }

    $.ajax({
        type        : 'POST' ,
        async       : false,
        url         : "/manager/project/workstatusupdate",
        data        : "Status="+ wstatus + "&mode=" + mode + "&ProjectWorkIdx="+widx,
        dataType    : 'JSON',
        success  : function(res) {
            if ( res.result !== true ) {
                alert('오류가 발생하였습니다');
                return false;
            }

            if ( res.TODOSTATICS.SUMCount > 0 ) {
                $("#Target_My_doing_Count").text(res.TODOSTATICS.SUMCount);
                $("#Target_My_doing_Count2").text(res.TODOSTATICS.SUMEmergency);
                $("#Target_My_doing_Count3").text(res.TODOSTATICS.SUMReopen);
                if ( res.TODOSTATICS.SUMDForetime>60 ) {
                    let SUMDForetime2 = res.TODOSTATICS.SUMDForetime/60;
                    $("#Target_My_doing_Count4").text(formatNumber(res.TODOSTATICS.SUMDForetime)+'분 ( ' + SUMDForetime2.toFixed(1) + ' 시간 )');
                } else{
                    if ( res.TODOSTATICS.SUMDForetime ===  null ) res.TODOSTATICS.SUMDForetime = 0;
                    $("#Target_My_doing_Count4").text(res.TODOSTATICS.SUMDForetime+'분');
                }
                $("#left_nav_todo").text(res.TODOSTATICS.SUMCount);

            }
            if ( res.DONESTATICS.SUMCount > 0 ) {
                $("#Target_My_done_Count").text(res.DONESTATICS.SUMCount);
                if ( res.DONESTATICS.SUMDoingTime>60 ) {
                    let SUMDoingTime2 = res.DONESTATICS.SUMDoingTime/60;
                    $("#Target_My_done_Count2").text(formatNumber(res.DONESTATICS.SUMDoingTime)+'분 ( ' + SUMDoingTime2.toFixed(1) + ' 시간 )');
                } else{
                    if ( res.DONESTATICS.SUMDoingTime ===  null ) res.TODOSTATICS.SUMDoingTime = 0;
                    $("#Target_My_done_Count2").text(res.DONESTATICS.SUMDoingTime+'분');
                }
                $("#left_nav_done").text(res.DONESTATICS.SUMCount);
            }
            if ( mode == 'doing' || mode == 'return'){
                $("#left_nav_doing").text("1");
            }else if ( mode == 'done' || mode == 'start'){
                $("#left_nav_doing").text("0");
            }
            return false;
        },
        error : function (ts) {
            alert(ts.responseStatus);
        }
    });
}

function fn_todo_update( widx) {

    let NowSourceName = $("#NowSourceName").val();
    let NowTargetName = $("#NowTargetName").val();
    if($("#NowDoingCount").val() == 1 && NowSourceName == '_todo' && NowTargetName == '_working') {
        ajax_todo_udpate('doing',widx);
    }else if($("#NowDoingCount").val() == 0 && NowSourceName == '_working' && NowTargetName == '_done') {
        ajax_todo_udpate('done',widx);
    }else if($("#NowDoingCount").val() == 1 && NowSourceName == '_done' && NowTargetName == '_working') {
        ajax_todo_udpate('return',widx);
    }else if($("#NowDoingCount").val() == 0 && NowSourceName == '_working' && NowTargetName == '_todo') {
        ajax_todo_udpate('start',widx);
    }

}

function fn_checkdoing(mode){
    $("#NowSourceName").val(mode);
    if($("#NowDoingCount").val() > 0 && mode !== '_working') {
        alert("현재 작업중인 프로젝트가 있습니다 \n동시에 두개의 프로젝트는 진행하실수 없습니다");
        //$(this).draggable( "disable" );
        $("#IsGoon").val(1);
        fn_stop();
        event.preventDefault();
        event.stopImmediatePropagation();
        return false;
    }


}

function fn_stop(event){
    event.preventDefault();
    event.stopImmediatePropagation();
    return false;
}

function fn_checkmust(mode,time,code,idx){
    var newtime = $("#Foretime_" + idx).val();
    var newcode = $("#ChildMode_" + idx).val();
    if(newtime == 0 && mode == '_todo') {
        alert("예상작업시간이 누락되었습니다.\n입력후 업무를 진행하십시요");
        //$(this).draggable( "disable" );
        fn_stop();
        event.preventDefault();
        event.stopImmediatePropagation();
        return false;
    }
    if(newcode == 0 && mode == '_todo') {
        alert("업무구분을 선택해 주십시요.\n입력후 업무를 진행하십시요");
        //$(this).draggable( "disable" );
        fn_stop();
        event.preventDefault();
        event.stopImmediatePropagation();
        return false;
    }
    //현재 Doing이 잇는지 조회
    if (  mode == '_todo' ) {
        fn_checkdoingcnt();
    }

}

function fn_checkdoingcnt() {
    jQuery.ajax({
        type: "POST",
        dataType: "json",
        url: "/manager/project/checkdoingcnt",
        data: "UserID=" + $("#UID").val(),
        async: false,
        success: function (json) {
            if (json.result === true) {
                if (json.count > 0 && $("#NowDoingCount").val() == 0) {
                    alert("현재 진행중인 업무가 있습니다. 강제로 페이지 리로딩합니다.");
                    location.reload();
                }
                return false;
            }

        }
    });
}

playAlert = setInterval(function() {
    jQuery.ajax({
        type: "POST",
        dataType: "json",
        url: "/manager/project/checkdoingcnt",
        data: "UserID=" + $("#UID").val(),
        async: false,
        success: function (json) {
            if (json.result === true) {
                if ( json.count > 1 ) {
                    clearInterval(playAlert);
                    alert("동시에 2개이상의 업무로 진행중입니다.");
                    location.reload();
                    return false;

                }
                if ( json.count != $("#NowDoingCount").val()) {
                    clearInterval(playAlert);
                    alert("현 페이지가 2곳이상에서 다르게 작업이 이루어 지고 있습니다. 페이지 리로딩합니다!");
                    location.reload();
                    return false;
                }
                return false;

            }
        }
    });
}, 10000);

function fn_dropaction(mode){
    $("#IsDoneOk").val(1);
    if ( mode == '_done' ) {
        if( !confirm("Done업무로 처리하시겠습니까?") )
        {
            $("#IsDoneOk").val(2);
            $(this).draggable( "disable" );
            event.preventDefault();
            event.stopImmediatePropagation();
            return;
        }else{
            $("#IsDoneOk").val(1);
        }
    }

    if ( mode == '_working' ) {
        $("#NowDoingCount").val(1);
        $("#IsGoon").val(1);
    }else{
        $("#NowDoingCount").val(0);
        $("#IsGoon").val(2);
    }
    $("#NowTargetName").val(mode);
    return false;
}

function fn_isconfirm(mode){

    if ( mode == '_done' ) {
        if( confirm("Done업무로 처리하시겠습니까??") )
        {
            $("#IsDoneOk").val(1);
        }else{
            $("#IsDoneOk").val(2);
            //$(this).draggable( "disable" );
            event.preventDefault();
            event.stopImmediatePropagation();
            return;

        }
    }else{
        $("#IsDoneOk").val(1);
    }
}


//TODO
$(document).on("click", ".btn_click_info", function () {
    let idx = $(this).data('idx');
    $("#popdetail").setLayer({
        'url' : '/manager/project/popdetail/' + idx + '/none',
        'width' : 1024,
        'max_height' : 500
    });
});

$(document).on("click", ".intra_page_link", function () {
    let go_url = $(this).data('url');
    window.open(go_url, '_blank');
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

