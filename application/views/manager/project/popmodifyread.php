<?include_once  $_SERVER["DOCUMENT_ROOT"].'/application/views/includes/popheader.php'; ?>

<link href="<?php echo base_url(); ?>assets/kanban/dist/hackerskanban.css" rel="stylesheet" type="text/css" />
<style>
    .group_wrap {clear:both;position:relative;width:100%;height:auto;}
    .group_wrap ul{clear:both;position:relative;width:96%;height:auto;min-height:40px;padding:5px 2%;}
    .group_wrap ul li{float:left;width:auto;min-width:100px;height:30px;padding:5px 10px;border-radius:10px;background-color: #fff;border:1px solid #ccc;list-style: none; text-align:center;margin-right:10px;margin-bottom:10px;cursor: pointer;color:#000;font-size:14px;}
    .group_wrap ul li.on{background-color: #605ca8;border:1px solid #605ca8;color:#fff}
</style>



<div class="modal-body">
    <div class="col-md-12">
        <form action="<?php echo base_url() ?>manager/project/update" method="POST" id="PopData">
            <input type="hidden" name="ProjectIdx"  id="ProjectIdx"  value="<?=$ProjectData['ProjectIdx']?>" >
            <input type="hidden" name="Permission"  id="Permission"  value="<?=$ProjectData['Permission']?>" >
            <input type="hidden" name="IsChat"  id="IsChat"  value="<?=$ProjectData['IsChat']?>" >
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
                                <td class="text-left"><?=$ProjectData['Register']?>[<?=$ProjectData['GROUPNAME']?>]</td>
                                <td class="table-dark text-center bg-gray">프로젝트번호</td>
                                <td class="text-left"><?=$ProjectData['ProjectNo']?></td>
                            </tr>
                            <tr>
                                <td class="table-active text-center bg-gray">프로젝트명</td>
                                <td class="text-left" colspan="3">
                                    <input type="text" class="form-control" id="ProjectTitle" name="ProjectTitle" value="<?=$ProjectData['ProjectTitle']?>" disabled="disabled">
                                </td>
                            </tr>
                            <tr>
                                <td class="table-active text-center bg-gray">구분</td>
                                <td class="text-left">
                                    <label for="ProjectMode" class="hidden">프로젝트구분</label>
                                    <select name="ProjectMode" id="ProjectMode" class="form-control changeProjectMode" disabled="disabled">
                                        <option value="2" <?=selected('2', @$ProjectData['ProjectMode'])?>>유지보수</option>
                                        <option value="1" <?=selected('1', @$ProjectData['ProjectMode'])?>>일반프로젝트</option>
                                        <option value="9" <?=selected('9', @$ProjectData['ProjectMode'])?>>기타</option>
                                    </select>
                                </td>
                                <td class="table-dark text-center bg-gray">진행상태</td>
                                <td class="text-left">
                                    <label for="ProjectStatus" class="hidden">진행상태</label>
                                    <select name="ProjectStatus" id="ProjectStatus" class="form-control" disabled="disabled">
                                        <option value="4" <?=selected('4', @$ProjectData['ProjectStatus'])?>>대기</option>
                                        <option value="1" <?=selected('1', @$ProjectData['ProjectStatus'])?>>진행중</option>
                                        <option value="2" <?=selected('2', @$ProjectData['ProjectStatus'])?>>완료</option>
                                        <option value="3" <?=selected('3', @$ProjectData['ProjectStatus'])?>>중단</option>

                                    </select>
                                </td>
                            </tr>
                            <tr id="viewProjectTeam" class=" <?=($ProjectData['ProjectMode']==2?'display_none':'')?>">
                                <td class="table-dark text-center bg-gray">팀선택</td>
                                <td class="text-left group_wrap" colspan="3">
                                    <ul>
                                        <?
                                        if ( $ProjectData['Permission'] ) {
                                            $Permission = json_decode($ProjectData['Permission']);
                                        }else{
                                            $Permission = array();
                                        }

                                        foreach( $GROUPCode as $key => $row) { ?>
                                            <li id="<?=$key?>" class="<?=(in_array( $key, $Permission)?'on':'')?>"><?=$row?></li>
                                        <? }?>
                                    </ul>

                                </td>
                            </tr>
                            <tr>
                                <td class="table-dark text-center bg-gray">옵션</td>
                                <td class="text-left group_wrap" colspan="3">
                                    <!--<ul>
                                        <li  class="<?/*=($ProjectData['IsChat']==1?'on':'')*/?>"><?/*=($ProjectData['IsChat']==1?'채팅사용':'채팅사용안함')*/?></li>
                                    </ul>-->
                                    ※ 유지보수는 해당팀에자동배정됩니다.

                                </td>
                            </tr>
                            <tr >
                                <td class="table-dark text-center bg-gray">참여중인 직원</td>
                                <td class="text-left group_wrap" colspan="3">
                                    <ul>
                                        <? if (count($ProjectMember) > 0 ) {
                                            foreach( $ProjectMember as $tkey => $tval ) {?>
                                                <li><?=$tval['GROUPNAME']?> <?=$tval['USERNAME']?></li>
                                            <? } ?>
                                        <? } ?>
                                    </ul>
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
        $(document).on("change", ".changeProjectMode", function () {
            if ( $(this).val() == 2 ) {
                //$("#srhGroup").find("option:eq(0)").prop("selected", true);
                $(".toggle_select").removeClass('on');
                $("#viewProjectTeam").addClass("display_none");
            }else{
                $("#viewProjectTeam").removeClass("display_none");
            }
        });

        $(document).on("click", ".toggle_select", function () {
            $(this).toggleClass('on');
        });

        $(document).on("click", ".toggle_chat", function () {

            if (  $(this).hasClass('on') ) {
                $(this).text('채팅사용안함');
            }else{
                $(this).text('채팅사용');
            }
            $(this).toggleClass('on');
        });

    });

</script>

<script type="text/javascript">
    jQuery(function($) {
        $(".modal-title").text("프로젝트 수정2");
        /*$(".modal-footer").append("<button type='button' class='btn btn-default' id='btn_pop_modify' data-mode='mod'>수정</button>");
        $(".modal-footer").append("<button type='button' class='btn btn-default' id='btn_pop_delete'>삭제</button>");*/
        $(".only_digit_str").bind("keyup",function(){
            $(this).val($(this).val().replace(/[^0-9]/g,""));
        });

        $(document).on("click", "#btn_pop_delete", function () {
            let $this = $(this);
            if( confirm("정말로 삭제하시겠습니까?") )
            {
                jQuery.ajax({
                    type        : "POST",
                    dataType    : "json",
                    url         : "/manager/project/delete",
                    data        : "ProjectIdx="+$('#ProjectIdx').val(),
                    async: false,
                    success: function(json){
                        if ( json.result === true ) {
                            alert( '삭제 되었습니다.');
                            $('#pop_modal').remove();
                            location.reload();
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

        $(document).on("click", "#btn_pop_modify", function () {

            let strmode =  '수정';
            let sendURL = "/manager/project/update";

            if ( $("#ProjectTitle").val() == "" ) {
                alert("프로젝트명은 필수항목입니다.");
                return false;
            }

            let permitssion_list = [];

            if ( $("#ProjectMode").val() == 2 ) {
                permitssion_list.push($("#MyGroup").val());
                $("#Permission").val(permitssion_list);
            }else{
                $.each( $(".toggle_select"), function(i,e ) {
                    if ( $(this).hasClass('on')) {
                        permitssion_list.push($(this).attr('id'))
                    }
                });
                if ( permitssion_list.length > 0 ) {
                    $("#Permission").val(permitssion_list);
                }
            }

            if (  $("#toggle_chat").hasClass('on') ) {
                $("#IsChat").val(1);
            }else{
                $("#IsChat").val(0);
            }

            if (  $("#toggle_use").hasClass('on') ) {
                $("#IsUse").val(1);
            }else{
                $("#IsUse").val(0);
            }

            let confirmation = confirm(strmode + "하시겠습니까?");

            if(confirmation)
            {
                let formData = new FormData($('#PopData')[0]);

                jQuery.ajax({
                    type : "POST",
                    dataType : "json",
                    url : sendURL,
                    data: formData,
                    async : false,
                    processData: false,
                    contentType: false,
                    success: function(json){
                        if ( json.result === true ) {
                            alert(strmode + '되었습니다.');
                            $('#pop_modal').remove();
                            location.reload();
                            return false;
                        } else {
                            alert('업데이트 처리중 오류가 발생하였습니다');
                            return false;
                        }
                    }
                });
            }
        });
    })
</script>

<?include_once  $_SERVER["DOCUMENT_ROOT"].'/application/views/includes/popfooter.php'; ?>



