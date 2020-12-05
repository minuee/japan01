<?include_once  $_SERVER["DOCUMENT_ROOT"].'/application/views/includes/popheader.php'; ?>
<div class="modal-body">
    <!--<div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">Panel Heading</div>
            <div class="panel-body">Panel Content</div>
        </div>
    </div>-->
    <div class="col-md-12">
        <form action="<?php echo base_url() ?>manager/schedule/update" method="POST" id="PopData">
            <input type="hidden" name="ScheduleIdx"  id="ScheduleIdx"  value="<?=$ScheduleData['ScheduleIdx']?>" >

            <div class="panel panel-default">
                <div class="panel-heading">
                    기본 정보
                    <small class="f_r text-red"><?=($ScheduleData['IsAgent'])?"대리등록 : ".$ScheduleData['AgentName']:""?></small>
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
                                <td class="table-active text-center bg-gray">등록자</td>
                                <td class="text-left"><?=$ScheduleData['UserName']?></td>
                                <td class="table-dark text-center bg-gray">팀명</td>
                                <td class="text-left"><?=$ScheduleData['GROUPNAME']?></td>
                            </tr>
                            <tr>
                                <td class="table-active text-center bg-gray">타이틀</td>
                                <td class="text-left">
                                    <? if ( $LoginSession['userId'] == $ScheduleData['RegID']  && date("Y-m-d") <= $ScheduleData['eDate']) {?>
                                        <input type="text" class="form-control" id="SubTitle" name="SubTitle" value="<?=$ScheduleData['SubTitle']?>" <?=($ScheduleData['Type']!=='50'?"disabled":"")?>>
                                    <? }else { ?>
                                        <input type="hidden"  name="SubTitle" id="SubTitle" value="<?=$ScheduleData['SubTitle']?>">
                                        <?=$ScheduleData['SubTitle']?>
                                    <? } ?>
                                </td>
                                <td class="table-active text-center bg-gray">구분</td>
                                <td class="text-left">
                                    <? if ( ( $LoginSession['userId'] == $ScheduleData['RegID'] && date("Y-m-d") <= $ScheduleData['eDate'] )  || $LoginSession['role'] == ROLE_ADMIN ) {?>
                                    <label for="Type" class="hidden">스케쥴구분</label>
                                    <select name="Type" id="Type" class="form-control changeScheduleType">
                                        <? foreach($CommonCode['Schedule'] as $key => $row ) {
                                            if ( $row['mode'] == 3 ) continue;
                                        ?>
                                            <option value="<?=$key?>" <?=selected($key, @$ScheduleData['Type'])?>><?=$row['name']?></option>
                                        <? } ?>
                                    </select>
                                    <? }else{?>
                                        <input type="hidden"  name="Type" id="Type" value="<?=$ScheduleData['Type']?>">
                                        <?=$CommonCode['Schedule'][$ScheduleData['Type']]['name']?>
                                    <? }?>
                                </td>
                            </tr>
                            <tr>
                                <td class="table-active text-center bg-gray">등록일</td>
                                <td class="text-left"><?=$ScheduleData['RegDatetime']?></td>
                                <td class="table-active text-center bg-gray">기간</td>
                                <td class="text-left"><?=$ScheduleData['sDate']?> ~ <?=$ScheduleData['eDate']?></td>
                            </tr>
                            <tr>
                                <td class="table-active text-center bg-gray">코멘트</td>
                                <td class="text-left" colspan="3">
                                    <? if ( ( ( $LoginSession['userId'] == $ScheduleData['RegID'] OR $LoginSession['userId'] == $ScheduleData['IsAgent']) && date("Y-m-d") <= $ScheduleData['eDate'] )  || $LoginSession['role'] == ROLE_ADMIN ) {?>
                                        <!--<input type="text" id="w_comment" class="form-control"  maxlength="50" value="<?/*=$ScheduleData['Comment']*/?>">-->
                                    <span class="f_l w_100">
                                        <textarea id="w_comment" name="w_comment" class="w_100" rows="5" cols="" maxlength="200" placeholder="특이사항 기입(200자내)"><?=$ScheduleData['Comment']?></textarea>
                                    </span>
                                    <!--<span class="f_l w_10"><button type='button' class='btn btn-default' id='btn_reg_reply'>수정</button></span>-->
                                    <?}else{?>
                                        <?=$ScheduleData['Comment']?>
                                    <? } ?>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>          
        </form>
    </div>

</div>

<script type='text/javascript'>
    $(function(){
        $(document).on("change", ".changeScheduleType", function () {
            if ( $(this).val() == 50 ) {
                $("#ProjectTitle").attr("disabled",false)
            }else{
                $("#ProjectTitle").attr("disabled",true)
            }
        });
    });

</script>


<script type="text/javascript">
    jQuery(function($) {
        $(".modal-title").text("일정관리 세부정보");
        <? if (  $LoginSession['role'] == ROLE_ADMIN ) {?>
            $(".modal-footer").append("<button type='button' class='btn btn-default' id='btn_delete'>삭제</button>");
            $(".modal-footer").append("<button type='button' class='btn btn-default' id='btn_pop_modify' data-mode='mod'>수정</button>");
        <? } else if ( ( $LoginSession['userId'] == $ScheduleData['RegID'] OR $LoginSession['userId'] == $ScheduleData['IsAgent']) && date("Y-m-d") <= $ScheduleData['eDate']) {?>
            $(".modal-footer").append("<button type='button' class='btn btn-default' id='btn_pop_modify' data-mode='mod'>수정</button>");
            $(".modal-footer").append("<button type='button' class='btn btn-default' id='btn_delete'>삭제</button>");
        <? } else if ( ( $LoginSession['userId'] == $ScheduleData['RegID'] OR $LoginSession['userId'] == $ScheduleData['IsAgent'])  && (strtotime($ScheduleData['RegDatetime'])+(60*60)) >= (time()) ) {?>
            $(".modal-footer").append("<button type='button' class='btn btn-default' id='btn_delete'>삭제</button>");
        <? } ?>

        $(document).off('click', '#btn_delete').on('click', '#btn_delete',function() {
            //let $this = $(this);
            if( confirm("정말로 삭제하시겠습니까?") )
            {
                let targetIdx = $('#ScheduleIdx').val();
                jQuery.ajax({
                    type        : "POST",
                    dataType    : "json",
                    url         : "/manager/schedule/delete",
                    data        : "ScheduleIdx="+targetIdx,
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

        });


        $(document).off('click', '#btn_pop_modify').on('click', '#btn_pop_modify',function() {

            if ( $(".changeScheduleType").val() == 50 && $("#ProjectTitle").val() == "" ) {
                alert("개인일정의 경우 타이틀은 필수입니다.");
                return false;
            }

            let confirmation201907291417 = confirm("수정하시겠습니까?");
            if(confirmation201907291417)
            {
                let targetIdx = $('#ScheduleIdx').val();
                let dd = $("#Type").val()

                $.ajax({
                    type : "POST",
                    dataType : "json",
                    url : "/manager/schedule/infoupdate",
                    data        : "ScheduleIdx="+targetIdx+"&Comment="+encodeURI($('#w_comment').val())+"&Type="+ $("#Type").val()+"&SubTitle="+encodeURI($('#SubTitle').val()),
                    async: false,
                    success: function(json){
                        if ( json.result === true ) {
                            alert('수정되었습니다.');
                            parent.reloaditem(targetIdx);
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

        /*$(document).off('click', '#btn_reg_reply').on('click', '#btn_reg_reply',function() {

            if ( $("#w_comment").val().length < 2 ){
                alert('최소2자이상 입력하세요');
                return false;
            }
            let confirmation = confirm("코멘트를 등록하시겠습니까?");

            if(confirmation )
            {
                jQuery.ajax({
                    type        : "POST",
                    dataType    : "json",
                    url         : "/manager/schedule/commentinsert",
                    data        : "ScheduleIdx="+$('#ScheduleIdx').val()+"&Comment="+encodeURI($('#w_comment').val()),
                    async: false,
                    success: function(json){
                        if ( json.result === true ) {
                            alert('정상등록되었습니다.');
                            return false;
                        } else {
                            alert('처리중 오류가 발생하였습니다');
                            return false;
                        }
                    }
                });
            }

            return false;
        });*/
    })
</script>

<?include_once  $_SERVER["DOCUMENT_ROOT"].'/application/views/includes/popfooter.php'; ?>



