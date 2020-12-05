<?include_once  $_SERVER["DOCUMENT_ROOT"].'/application/views/includes/popheader.php'; ?>
<div class="modal-body" style="float:left">
    <!--<div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">Panel Heading</div>
            <div class="panel-body">Panel Content</div>
        </div>
    </div>-->
    <div class="col-md-12">
        <form action="<?php echo base_url() ?>manager/project/workupdate" method="POST" id="PopData">
            <input type="hidden" name="ProjectWorkIdx"  id="ProjectWorkIdx"  value="<?=$ProjectWorkIdx?>" >
            <input type="hidden" name="ModifyMode"  id="ModifyMode"  value="0" >
            <input type="hidden" name="OriginsDate"   value="<?=$ProjectData['sDate']?>" >
            <input type="hidden" name="OrigineDate"   value="<?=$ProjectData['eDate']?>" >
            <input type="hidden" name="OriginStatus"   value="<?=$ProjectData['Status']?>" >
            <input type="hidden" name="OriginToDoID"   value="<?=$ProjectData['ToDoID']?>" >
            <input type="hidden" name="OriginRate"   value="<?=$ProjectData['Rate']?>" >
            <input type="hidden" name="ToDoID"   value="<?=$ProjectData['ToDoID']?>" >
            <input type="hidden" name="Modifyer"   value="<?=$LoggedInfo['role']?>" >

            <div class="panel panel-default">
                <div class="panel-heading">기본 정보</div>
                <!--<div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool text-white" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                        <i class="fa fa-plus" id="member_detail_info_icon"></i>
                    </button>
                </div>-->
                <div class="panel-body">
                    <div class="box-body p-sm">
                        <table class="table table-bordered">
                            <colgroup>
                                <col style="width:15%;">
                                <col style="width:35%;">
                                <col style="width:15%;">
                                <col style="width:35%;">
                            </colgroup>
                            <tbody>
                            <tr>
                                <td class="table-active text-center bg-gray">생성자</td>
                                <td class="text-left"><?=$ProjectData['Register']?></td>
                                <td class="table-dark text-center bg-gray">프로젝트번호</td>
                                <td class="text-left"><?=$ProjectData['ProjectNo']?></td>
                            </tr>
                            <tr>
                                <td class="table-active text-center bg-gray">프로젝트명</td>
                                <td class="text-left" colspan="3">
                                    <label for="ProjectIdx" class="hidden">프로젝트 선택</label>
                                    <select class="form-control noh_text_14 w_100" id="ProjectIdx" name="ProjectIdx">
                                        <? foreach ( $ProjectList as  $key => $val ) {?>
                                            <option value="<?=$val['ProjectIdx']?>" <?=selected($val['ProjectIdx'], $ProjectData['ProjectIdx'])?>> <?=$val['ProjectTitle']?></option>
                                        <? } ?>
                                    </select>
                                </td>

                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    Todo 내용
                    <span class="f_r">
                        <? if ( $ProjectData['IsReOpen']==1) {?>
                            <img src="/assets/images/reopen.png" height="20"> 재작업(ReOpen)
                        <? } ?>
                        <? if ( $ProjectData['IsReOpen']==2) {?>
                            <img src="/assets/images/together.png" height="20"> 협업작업
                        <? } ?>
                    </span>
                </div>
                <div class="panel-body">
                    <div class="box-body p-sm">
                        <table class="table table-bordered">
                            <colgroup>
                                <col style="width:15%;">
                                <col style="width:35%;">
                                <col style="width:15%;">
                                <col style="width:35%;">
                            </colgroup>
                            <tbody>
                            <tr>
                                <td class="table-active text-center bg-gray">작업내역</td>
                                <td class="text-left" colspan="3">
                                    <input type="text" name="title" class="form-control " value="<?=$ProjectData['title']?>" >
                                </td>
                            </tr>

                            <tr>
                                <td class="table-active text-center bg-gray">생성자</td>
                                <td class="text-left">
                                    <?=$ProjectData['Indicator']?>

                                </td>
                                <td class="table-dark text-center bg-gray">작업자</td>
                                <td class="text-left">
                                    <? if ( $LoggedInfo['role'] == ROLE_SUPERVISOR ) { ?>
                                        <label for="ToDoID" class="hidden">작업자</label>
                                        <select name="ToDoID" id="ToDoID" class="form-control">
                                            <option value="0">작업자선택</option>
                                            <? foreach ( $Users as  $key => $val ) {?>
                                                <option value="<?=$val['userId']?>" <?=selected($ProjectData['ToDoID'], @$val['userId'])?>>[<?=$val['GROUP_NAME']?>] <?=$val['name']?> <?=$val['CLASS_NAME']?></option>
                                            <? }?>
                                        </select>
                                    <? }else if ( $LoggedInfo['role'] == ROLE_MANAGER && $LoggedInfo['userId'] == $ProjectData['RegID']) { ?>
                                        <label for="ToDoID" class="hidden">작업자</label>
                                        <select name="ToDoID" id="ToDoID" class="form-control">
                                            <option value="0">작업자선택</option>
                                            <? foreach ( $Users as  $key => $val ) {?>
                                                <option value="<?=$val['userId']?>" <?=selected($ProjectData['ToDoID'], @$val['userId'])?>>[<?=$val['GROUP_NAME']?>] <?=$val['name']?> <?=$val['CLASS_NAME']?></option>
                                            <? }?>
                                        </select>

                                    <?}else{ ?>
                                        <?=$ProjectData['Commander']?>
                                    <? }?>
                                </td>
                            </tr>

                            <tr>
                                <td class="table-active text-center bg-gray">시작일</td>
                                <td class="text-left"><?=$ProjectData['sDate']?>
                                    <!--<div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" class="form-control noh_datepicker" id="search_start_date" name="sDate" value="<?/*=$ProjectData['sDate']*/?>" <?/*=($LoggedInfo['role'] == ROLE_EMPLOYEE?'disabled':'')*/?>>
                                    </div>-->

                                </td>
                                <td class="table-dark text-center bg-gray">종료일</td>
                                <td class="text-left"><?=$ProjectData['eDate']?>
                                    <!--<div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" class="form-control noh_datepicker" id="search_end_date" name="eDate" value="<?/*=$ProjectData['eDate']*/?>" <?/*=($LoggedInfo['role'] == ROLE_EMPLOYEE?'disabled':'')*/?>>
                                    </div>-->
                                </td>
                            </tr>
                            <tr>
                                <td class="table-active text-center bg-gray">구분</td>
                                <td class="text-left">
                                    <label for="ChildMode" class="hidden">업무구분</label>
                                    <select class="form-control noh_text_14 w_100" id="ChildMode" name="ChildMode">
                                        <option value="">없음</option>
                                        <? foreach ( $CommonCode['ChildMode'] as $key => $val ) {?>
                                            <option value="<?=$key?>" <?=selected($key, @$ProjectData['ChildMode'])?>><?=$val['name']?></option>
                                        <?} ?>
                                    </select>
                                </td>
                                <td class="table-dark text-center bg-gray">우선순위</td>
                                <td class="text-left">
                                    <label for="Priority" class="hidden">우선순위</label>
                                    <select class="form-control noh_text_14 w_100" id="Priority" name="Priority">
                                        <? foreach ( $CommonCode['Priority'] as $key => $val ) {?>
                                            <option value="<?=$key?>" <?=selected($key, @$ProjectData['Priority'])?>><?=$val['name']?></option>
                                        <?} ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td class="table-active text-center bg-gray">상태</td>
                                <td class="text-left"><?=($ProjectData['Status']?$CommonCode['WorksStatus'][$ProjectData['Status']]['name']:null)?>
                                    <!--<label for="Status" class="hidden">진행상태</label>
                                    <select name="Status" id="Status" class="form-control" <?/*=($LoggedInfo['role'] == ROLE_EMPLOYEE || $LoggedInfo['role'] == ROLE_MANAGER ?'disabled':'')*/?>>
                                        <option value="1" <?/*=selected('1', @$ProjectData['Status'])*/?>>대기</option>
                                        <option value="2" <?/*=selected('2', @$ProjectData['Status'])*/?>>진행중</option>
                                        <option value="3" <?/*=selected('3', @$ProjectData['Status'])*/?>>중단</option>
                                        <option value="9" <?/*=selected('9', @$ProjectData['Status'])*/?>>완료</option>
                                    </select>-->
                                </td>
                                <td class="table-dark text-center bg-gray">진척도(%)</td>
                                <td class="text-left">
                                    <? if ( $ProjectData['Status'] == 9 ) {?>
                                        <?=$ProjectData['Rate']?>
                                    <?}else{?>
                                        <input type="number" name="Rate" class="form-control only_digit_str" value="<?=$ProjectData['Rate']?>" <?=($LoggedInfo['role'] == ROLE_EMPLOYEE && $LoggedInfo['userId']!==$ProjectData['ToDoID']?'disabled':'')?> min="0" max="100" step="10">
                                    <?} ?>

                                </td>
                            </tr>
                            <tr>
                                <td class="table-dark text-center bg-gray">에상작업시간(분)</td>
                                <td class="text-left" colspan="3">
                                    <? if ( $ProjectData['Foretime'] > 0 ) {?>
                                        <input type="hidden" name="Foretime" id="TargetForetime" value="<?=$ProjectData['Foretime']?>" >
                                        <?=number_format($ProjectData['Foretime'])?>
                                        <? if ($ProjectData['Foretime']>60 ) echo " (".number_format($ProjectData['Foretime']/60,1)." 시간)"?>
                                    <? }else{?>
                                        <span class="f_l w_30">
                                            <input type="number" name="Foretime" id="TargetForetime" class="form-control only_digit_str w_80" value="<?=$ProjectData['Foretime']?>" placeholder="최초등록후 수정불가" step="10">
                                        </span>
                                        <span class="f_l w_70">
                                            <span class="f_l w_10"><button type='button' class='btn btn-default btn-set-time' data-time="10">10분</button></span>
                                            <span class="f_l w_10"><button type='button' class='btn btn-default btn-set-time' data-time="30">30분</button></span>
                                            <span class="f_l w_10"><button type='button' class='btn btn-default btn-set-time' data-time="60">1시간</button></span>
                                            <span class="f_l w_10"><button type='button' class='btn btn-default btn-set-time' data-time="120"'>2시간</button></span>
                                            <span class="f_l w_10"><button type='button' class='btn btn-default btn-set-time' data-time="240"'>4시간</button></span>
                                            <span class="f_l w_10"><button type='button' class='btn btn-default btn-set-time' data-time="480"'>1일</button></span>
                                            <span class="f_l w_10"><button type='button' class='btn btn-default btn-set-time' data-time="960"'>2일</button></span>
                                            <span class="f_l w_10"><button type='button' class='btn btn-default btn-set-time' data-time="1440"'>3일</button></span>
                                            <span class="f_l w_10"><button type='button' class='btn btn-default btn-set-time' data-time="1920"'>4일</button></span>
                                            <span class="f_l w_10"><button type='button' class='btn btn-default btn-set-time' data-time="2400"'>5일</button></span>
                                        </span>

                                    <? } ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="table-dark text-center bg-gray">투입시간</td>
                                <td class="text-left" colspan="3">
                                    <?=($ProjectData['SUMDoingTime']>0?$ProjectData['SUMDoingTime']:0)?> 분
                                    <? if ($ProjectData['SUMDoingTime']>60 ) echo " (".number_format($ProjectData['SUMDoingTime']/60,1)." 시간)"?>
                                </td>
                            </tr>
                            <tr>
                                <td class="table-dark text-center bg-gray">글자색상</td>
                                <td>
                                    <label for="Background" class="hidden">색상정보</label>
                                    <select class="form-control noh_text_14 w_30" id="Background" name="Background">
                                        <option value="" class="bg-post-default">기본색</option>
                                        <? foreach ( $CommonCode['WorkBackground'] as $key => $val ) {?>
                                            <option value="<?=$val['class']?>" <?=selected($val['class']."2", @$ProjectData['Background']."2")?>  class="<?=$val['class']?>2"><?=$val['name']?></option>
                                        <?} ?>
                                    </select>
                                </td>
                                <td class="table-dark text-center bg-gray">배경색상</td>
                                <td >
                                    <label for="TargetPostColor" class="hidden">색상정보</label>
                                    <select class="form-control noh_text_14 w_30" id="TargetPostColor" name="PostColor">
                                        <option value="" class="bg-post-default">기본색</option>
                                        <? foreach ( $CommonCode['WorkPostColor'] as $key => $val ) {?>
                                            <option value="<?=$val['class']?>" <?=selected($val['class'], @$ProjectData['PostColor'])?>  class="<?=$val['class']?>"><?=$val['name']?></option>
                                        <?} ?>
                                    </select>
                                </td>
                            </tr>

                            <tr>
                                <td class="table-dark text-center bg-gray">인트라넷</td>
                                <td  colspan="3"><input type="text" id="IntraUrl" name="IntraUrl" class="form-control" placeholder="인트라넷 바로기기 주소 입력" value="<?=$ProjectData['IntraUrl']?>" <?=$ProjectData['IntraBoard']?"disabled":""?>></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="col-md-12 collapsed-box">
        <div class="panel panel-default ">
            <div class="panel-heading" role="tab" id="headingOne">
                Memo
                <span style="position:relative;float:left;width:100%;height:1px;">
                    <span style="position:absolute;right:5px;bottom:0">
                        <button type="button" class="btn btn-box-tool text-white btn-toggle-ox" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                            <i class="fa fa-minus" ></i>
                        </button>
                    </span>
                </span>
            </div>
            <div id="collapseOne" class="panel-body panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
                <div class="box-body p-sm">
                    <ul class="list-group" id="Pop_Wrapper_Replylist">
                        <!--<li class="list-group-item d-flex justify-content-between align-items-center">
                            (공지)100자내외로 간략한 업무보고 또는 메모시 이용하세요.
                            <span class="badge badge-primary badge-pill">2019-06-30 15:10</span>
                            <span class="badge badge-primary badge-pill">관리자</span>
                        </li>-->
                        <? foreach ( $Replys as  $key => $val ) {?>
                            <li class="f_l list-group-item d-flex justify-content-between align-items-center w_100">
                                <span style="float:left;width:70% !important;min-width:70% !important;"><?=$val['Comment']?></span>
                                <span class="badge badge-primary badge-pill btn_delete_reply noh_cursor <?=$LoggedInfo['userId']==$val['RegID']?'':'display_none'?>" data-idx='<?=$val['ProjectWorkCommentIdx']?>'>X</span>
                                <span class="badge badge-primary badge-pill"><?=$val['RegName']?></span>
                                <span class="badge badge-primary badge-pill"><?=$val['RegDatetime']?></span>
                            </li>
                        <? } ?>


                    </ul>
                    <span class="f_l w_100 mt_10">
                        <span class="f_l w_90 "><input type="text" id="w_commnet" class="form-control" placeholder="지시 또는 확인사항등을 입력하세요""></span>
                        <span class="f_l w_10"><button type='button' class='btn btn-default' id='btn_reg_reply'>입력</button></span>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-12 collapsed-box">
        <div class="panel panel-default ">
            <div class="panel-heading" role="tab" id="headingTwo">
                History
                <span style="position:relative;float:left;width:100%;height:1px;">
                    <span style="position:absolute;right:5px;bottom:0">
                        <button type="button" class="btn btn-box-tool text-white btn-toggle-ox" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                            <i class="fa fa-plus" ></i>
                        </button>
                    </span>
                </span>
            </div>
            <div id="collapseTwo" class="panel-body panel-collapse collapse out" role="tabpanel" aria-labelledby="headingTwo">
                <div class="box-body p-sm">
                    <ul class="list-group" id="Pop_Wrapper_Replylist">
                        <? foreach ( $Historys as  $key => $val ) {
                            $_jsondata = json_decode($val['sessionData']);
                            ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?=$_jsondata->message?>
                                <span class="badge badge-primary badge-pill"><?=$val['createdDtm']?></span>
                                <span class="badge badge-primary badge-pill"><?=$_jsondata->regname?></span>

                            </li>
                        <? } ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>



</div>

<script src="<?php echo base_url(); ?>assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/bootstrap-datepicker.kr.js" charset="UTF-8"></script>
<script type='text/javascript'>
    $(function(){
        $('.noh_datepicker').datepicker({
            calendarWeeks: false,
            todayHighlight: true,
            autoclose: true,
            format: "yyyy-mm-dd",
            language: "kr"
        });

        $("#Background").change(function(){
            $("#Background").removeClass('bg-post-default2 bg-post-green2 bg-post-skyblue2 bg-post-pink2 bg-post-red2 bg-post-red2 bg-post-black2');
            var color = $("option:selected", this).val();
            if ( color == "" ) {
                $("#Background").addClass('bg-post-default2');
            }else{
                $("#Background").addClass(color+'2');
            }
        });

        $(".btn-set-time").click(function(){
            $("#TargetForetime").val($(this).data('time'));
            return false;
        });
    });

</script>

<script type="text/javascript">
    jQuery(function($) {
        $(".modal-title").text("프로젝트 업무 정보1");
        /*$(".modal-footer").append("<button type='button' class='btn btn-default' id='btn_modify'>수정</button>");
        $(".modal-footer").append("<button type='button' class='btn btn-default' id='btn_delete'>삭제</button>");*/

        <? if ( $LoggedInfo['userId'] == $ProjectData['ProjectRegID']  ) { ?>
            $(".modal-footer").append("<button type='button' class='btn btn-primary btn_modify'>수정</button>");
        <? }else if ( $LoggedInfo['userId'] == $ProjectData['RegID'] && empty($ProjectData['ToDoID']) && $ProjectData['Status'] == 1 ) { ?>
            $(".modal-body").prepend("<span class='f_r mb_10'><button type='button' class='btn btn-default mr_10 btn_modify'>수정</button><button type='button' class='btn btn-default mr_10 btn_delete'>삭제</button><button type='button' class='btn btn-primary' id='btn_work_copy'>복제</button></span>");
            $(".modal-footer").append("<button type='button' class='btn btn-default btn_modify'>수정</button>");
            $(".modal-footer").append("<button type='button' class='btn btn-default btn_delete'>삭제</button>");

        <?}else if ( $LoggedInfo['userId'] == $ProjectData['ToDoID'] &&  $ProjectData['Status'] == 1) { ?>
            $(".modal-body").prepend("<span class='f_r mb_10'><button type='button' class='btn btn-default mr_10 btn_modify'>수정</button><button type='button' class='btn btn-default btn_delete'>삭제</button></span>");
            $(".modal-footer").append("<button type='button' class='btn btn-default btn_modify'>수정</button>");
            $(".modal-footer").append("<button type='button' class='btn btn-default btn_delete'>삭제</button>");
        <?}else if ( $LoggedInfo['userId'] == $ProjectData['ToDoID'] &&  $ProjectData['Status'] !== 1) { ?>
            $(".modal-body").prepend("<span class='f_r mb_10'><button type='button' class='btn btn-default mr_10 btn_modify'>수정</button></span>");
            $(".modal-footer").append("<button type='button' class='btn btn-default btn_modify'>수정</button>");
        <?}else if ( $LoggedInfo['userId'] == $ProjectData['RegID'] &&  $ProjectData['Status'] !== 1) { ?>
            $(".modal-body").prepend("<span class='f_r mb_10'><button type='button' class='btn btn-default mr_10 btn_modify'>수정</button></span>");
            $(".modal-footer").append("<button type='button' class='btn btn-default btn_modify'>수정</button>");
        <?}else{ ?>

        <? }?>
        $(".only_digit_str").bind("keyup",function(){
            $(this).val($(this).val().replace(/[^0-9]/g,""));
        });

        $(document).off('click', '#btn_work_copy').on('click', '#btn_work_copy',function() {
            let targetIdx = $('#ProjectWorkIdx').val();
            if( confirm("지금 업무를 복제 하시겠습니까?") )
            {
                var clonecnt = prompt('복사할 수를 입력하세요. (최대 10개)');
                if ( !$.isNumeric(clonecnt) ){
                    alert("복사할 수량이 정확하지 않습니다. \r정수만 입력해주세요");
                    return false;
                }

                jQuery.ajax({
                    type        : "POST",
                    dataType    : "json",
                    url         : "/manager/project/workcopy",
                    data        : "ProjectWorkIdx="+targetIdx+"&CloneCount="+parseInt(clonecnt),
                    async: false,
                    beforeSend: function () {
                        $('.wrap-loading').removeClass('display_none');
                    },
                    success: function(json){
                        $('.wrap-loading').addClass('display_none');
                        if ( json.result === true ) {
                            alert('복제가 완료되었습니다. \n페이지 리로딩합니다.');
                            parent.location.reload();
                            return false;
                        } else {
                            alert('처리중 오류가 발생하였습니다');
                            return false;
                        }

                    },
                    complete: function () {
                        $('.wrap-loading').addClass('display_none');
                    }
                });
            }
            return false;
        });

        $(document).off('click', '.btn_delete').on('click', '.btn_delete',function() {
            let targetIdx = $('#ProjectWorkIdx').val();
            if( confirm("정말로 삭제하시겠습니까?") )
            {
                jQuery.ajax({
                    type        : "POST",
                    dataType    : "json",
                    url         : "/manager/project/workdelete",
                    data        : "ProjectWorkIdx="+targetIdx,
                    async: false,
                    success: function(json){
                        if ( json.result === true ) {
                            parent.removeitem(targetIdx);
                            $(".close_modal_btn").trigger('click');
                            return false;
                        } else {
                            alert('처리중 오류가 발생하였습니다');
                            return false;
                        }
                    }
                });
            }
            return false;
        });

        $(document).off('click', '.btn_delete_reply').on('click', '.btn_delete_reply',function() {
            let $this = $(this);
            if( confirm("메모를 삭제하시겠습니까?") )
            {
                $.ajax({
                    type        : "POST",
                    dataType    : "json",
                    url         : "/manager/project/replydelete",
                    data        : "ProjectWorkCommentIdx="+$(this).data('idx'),
                    async: false,
                    success: function(json){
                        if ( json.result === true ) {
                            $this.closest('li').remove();
                            return false;
                        } else {
                            alert('처리중 오류가 발생하였습니다');
                            return false;
                        }
                    }
                });
            }
            return false;
        });

        $(document).off('click', '#btn_reg_reply').on('click', '#btn_reg_reply',function() {
            if ( $("#w_commnet").val().length < 1 ){
                alert('최소1자이상 입력하세요');
                return false;
            }
            let confirmation = confirm("메모를 등록하시겠습니까?");

            if(confirmation )
            {
                $.ajax({
                    type        : "POST",
                    dataType    : "json",
                    url         : "/manager/project/replyinsert",
                    //data        : "ProjectWorkIdx="+$('#ProjectWorkIdx').val()+"&Comment="+encodeURI($('#w_commnet').val()),
                    data        : {
                        "ProjectWorkIdx" : $('#ProjectWorkIdx').val(),
                        "Comment" : encodeURI($('#w_commnet').val())
                    },
                    async: false,
                    success: function(json){
                        if ( json.result === true ) {
                            let addhtml = "<li class='f_l list-group-item d-flex justify-content-between align-items-center w_100'><span style='float:left;width:70% !important;min-width:70% !important;'>최근메모 : "+$("#w_commnet").val()+"</span><span class='badge badge-primary badge-pill btn_delete_reply noh_cursor' data-idx='"+json.result_idx+"'>X</span><span class='badge badge-primary badge-pill'>"+json.RegName + "</span><span class='badge badge-primary badge-pill'>"+json.RegDatetime + "</span></li>";
                            $("#Pop_Wrapper_Replylist").append(addhtml);
                            $("#w_commnet").val("");
                            return false;
                        } else {
                            alert('처리중 오류가 발생하였습니다');
                            return false;
                        }
                    }
                });
            }
        });


        $(document).off('click', '.btn_modify').on('click', '.btn_modify',function() {

            if ( $('#Foretime').val() < 1) {
                alert("예상소요시간을 입력해주세요");
                $('#Foretime').focus();
                return false;
            }

            let confirmation201907291121 = confirm("수정하시겠습니까?");
            if(confirmation201907291121)
            {
                $("#ModifyMode").val(1);
                let targetIdx = $('#ProjectWorkIdx').val();
                let formData = new FormData($('#PopData')[0]);
                let hitURL = "/manager/project/workupdate";
                $.ajax({
                    type : "POST",
                    dataType : "json",
                    url : hitURL,
                    data: formData,
                    processData: false,
                    contentType: false,
                    async: false,
                    success: function(json){
                        if ( json.result === true ) {
                            alert('수정되었습니다.');
                            //parent.reloaditem(targetIdx);
                            parent.location.reload();
                            $(".close_modal_btn").trigger('click');
                            return true;
                        } else {
                            alert('업데이트 처리중 오류가 발생하였습니다');
                            return true;

                        }
                    },
                    error: function(){
                        alert('업데이트 처리중 오류가 발생하였습니다');
                        return false;
                    },
                    complete: function(){
                        return false;
                    }
                });
            }
        });


        $('.btn_modify').on('hidden.bs.collapse', function () {
            // do something…
            alert(1);
        });

        $('#list_table').off('click', '.btn-income-send').on('click', '.btn-income-send',function() {
            $("#member_detail_info").addClass('collapsed-box');
            $("#member_detail_info_icon").removeClass('fa-minus').addClass('fa-plus');
            $("#member_detail_info_tbody").css("display", "none");
        })
    })
</script>

<?include_once  $_SERVER["DOCUMENT_ROOT"].'/application/views/includes/popfooter.php'; ?>



