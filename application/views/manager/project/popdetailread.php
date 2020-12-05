<?include_once  $_SERVER["DOCUMENT_ROOT"].'/application/views/includes/popheader.php'; ?>
<div class="modal-body w_100" style="float:left">
    <!--<div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">Panel Heading</div>
            <div class="panel-body">Panel Content</div>
        </div>
    </div>-->
    <div class="col-md-12">
        <form action="<?php echo base_url() ?>manager/income/update" method="POST" id="PopData">
            <input type="hidden" name="ProjectWorkIdx"  id="ProjectWorkIdx"  value="<?=$ProjectWorkIdx?>" >
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
                                <td class="text-left" colspan="3"><?=$ProjectData['ProjectTitle']?></td>

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
                                <td class="text-left" colspan="3"><?=$ProjectData['title']?></td>
                            </tr>
                            <tr>
                                <td class="table-active text-center bg-gray">생성자</td>
                                <td class="text-left"><?=$ProjectData['Indicator']?>

                                </td>
                                <td class="table-dark text-center bg-gray">작업자</td>
                                <td class="text-left"><?=$ProjectData['Commander']?></td>
                            </tr>
                            <tr>
                                <td class="table-active text-center bg-gray">시작일</td>
                                <td class="text-left"><?=$ProjectData['sDate']?></td>
                                <td class="table-dark text-center bg-gray">종료일</td>
                                <td class="text-left"><?=$ProjectData['eDate']?></td>
                            </tr>
                            <tr>
                                <td class="table-active text-center bg-gray">구분</td>
                                <td class="text-left"><?=($ProjectData['ChildMode']?$CommonCode['ChildMode'][$ProjectData['ChildMode']]['name']:null)?></td>
                                <td class="table-dark text-center bg-gray">우선순위</td>
                                <td class="text-left"><?=($ProjectData['Priority']?$CommonCode['Priority'][$ProjectData['Priority']]['name']:null)?></td>
                            </tr>
                            <tr>
                                <td class="table-active text-center bg-gray">상태</td>
                                <td class="text-left"><?=($ProjectData['Status']?$CommonCode['WorksStatus'][$ProjectData['Status']]['name']:null)?></td>
                                <td class="table-dark text-center bg-gray">진척도(%)</td>
                                <td class="text-left"><?=$ProjectData['Rate']?>%</td>
                            </tr>
                            <tr>
                                <td class="table-dark text-center bg-gray">에상작업시간(분)</td>
                                <td class="text-left"><?=$ProjectData['Foretime']?></td>
                                <td class="table-dark text-center bg-gray">투입시간</td>
                                <td class="text-left">
                                    <?=($ProjectData['SUMDoingTime']>0?$ProjectData['SUMDoingTime']:0)?> 분
                                    <? if ($ProjectData['SUMDoingTime']>60 ) echo " (".number_format($ProjectData['SUMDoingTime']/60,1)." 시간)"?>
                                </td>
                            </tr>
                            <tr>
                                <td class="table-dark text-center bg-gray">인트라넷</td>
                                <td  colspan="3"><?=$ProjectData['IntraUrl']?></td>
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
                            (공지)50자내외로 간략한 업무보고 또는 지시때 이용하세요.
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
                                <span class="badge badge-primary badge-pill"><?=$_jsondata->regname?></span>
                                <span class="badge badge-primary badge-pill"><?=$val['createdDtm']?></span>
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
<script src="<?php echo base_url(); ?>assets/js/jscolor.js?v=<?=time()?>"></script>
<script type='text/javascript'>
    $(function(){
        $('.noh_datepicker').datepicker({
            calendarWeeks: false,
            todayHighlight: true,
            autoclose: true,
            format: "yyyy-mm-dd",
            language: "kr"
        });
    });

</script>

<script type="text/javascript">
    jQuery(function($) {
        $(".modal-title").text("프로젝트 업무 조회");


        $(document).on("click", ".btn_delete_reply", function () {
            let $this = $(this);
            if( confirm("코멘트를 삭제하시겠습니까?") )
            {
                jQuery.ajax({
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

        $(document).on("click", "#btn_reg_reply", function () {

            if ( $("#w_commnet").val().length < 1 ){
                alert('최소1자이상 입력하세요');
                return false;
            }
            let confirmation = confirm("코멘트를 등록하시겠습니까?");

            if(confirmation )
            {
                jQuery.ajax({
                    type        : "POST",
                    dataType    : "json",
                    url         : "/manager/project/replyinsert",
                    data        : {
                        "ProjectWorkIdx" : $('#ProjectWorkIdx').val(),
                        "Comment" : encodeURI($('#w_commnet').val())
                    },
                    //data        : "ProjectWorkIdx="+$('#ProjectWorkIdx').val()+"&Comment="+encodeURI($('#w_commnet').val()),
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


        $('#list_table').off('click', '.btn-income-send').on('click', '.btn-income-send',function() {
            $("#member_detail_info").addClass('collapsed-box');
            $("#member_detail_info_icon").removeClass('fa-minus').addClass('fa-plus');
            $("#member_detail_info_tbody").css("display", "none");
        })
    })
</script>

<?include_once  $_SERVER["DOCUMENT_ROOT"].'/application/views/includes/popfooter.php'; ?>



