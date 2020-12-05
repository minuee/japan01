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
            <input type="hidden" name="viewmode"   value="<?=$viewmode?>" >

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
                                <td class="table-active text-center bg-gray">프로젝트</td>
                                <td class="text-left" colspan="3">
                                    <label for="ProjectIdx" class="hidden">프로젝트 선택</label>
                                    <select class="form-control noh_text_14 w_100" id="ProjectIdx" name="ProjectIdx">
                                        <? foreach ( $ProjectList as  $key => $val ) {?>
                                            <option value="<?=$val['ProjectIdx']?>" <?=selected($val['ProjectIdx'], $ProjectData['ProjectIdx'])?>> <?=$val['ProjectTitle']?></option>
                                        <? } ?>
                                    </select>
                                    <?/*=$ProjectData['ProjectTitle']*/?>
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
                                <td class="text-left" colspan="3"><input type="text" name="title" class="form-control " value="<?=$ProjectData['title']?>" ></td>
                            </tr>

                            <tr>
                                <td class="table-active text-center bg-gray">생성자</td>
                                <td class="text-left">
                                    <?=$ProjectData['Indicator']?>

                                </td>
                                <td class="table-dark text-center bg-gray">작업자</td>
                                <td class="text-left"><?=$ProjectData['Commander']?>
                                   <!-- <?/* if ( $LoggedInfo['role'] == ROLE_ADMIN ||  $LoggedInfo['role'] == ROLE_SUPERVISOR ) { */?>
                                        <label for="ToDoID" class="hidden">작업자</label>
                                        <select name="ToDoID" id="ToDoID" class="form-control">
                                            <option value="0">작업자선택</option>
                                            <?/* foreach ( $Users as  $key => $val ) {*/?>
                                                <option value="<?/*=$val['userId']*/?>" <?/*=selected($ProjectData['ToDoID'], @$val['userId'])*/?>>[<?/*=$val['GROUP_NAME']*/?>] <?/*=$val['name']*/?></option>
                                            <?/* }*/?>
                                        </select>
                                    <?/* }else if ( $LoggedInfo['role'] == ROLE_MANAGER && $LoggedInfo['userId'] == $ProjectData['RegID']) { */?>
                                        <label for="ToDoID" class="hidden">작업자</label>
                                        <select name="ToDoID" id="ToDoID" class="form-control">
                                            <option value="0">작업자선택</option>
                                            <?/* foreach ( $Users as  $key => $val ) {*/?>
                                                <option value="<?/*=$val['userId']*/?>" <?/*=selected($ProjectData['ToDoID'], @$val['userId'])*/?>>[<?/*=$val['GROUP_NAME']*/?>] <?/*=$val['name']*/?></option>
                                            <?/* }*/?>
                                        </select>

                                    <?/*}else{ */?>
                                        <?/*=$ProjectData['Commander']*/?>
                                    --><?/* }*/?>

								<? if( $_SERVER['REMOTE_ADDR'] == '172.16.0.15' || $_SERVER['REMOTE_ADDR'] == '172.16.1.15' ){ ?>
									<br />
									<span class="f_l" style=""><button type='button' class='btn btn-default btn-default-setting' style="font-size:12px;padding:0;color:#00f;" data-setting='99|10|bg-post-yeondo2'>기타_10분_연두색</button></span>
								<? }else if( $_SERVER['REMOTE_ADDR'] == '172.16.0.116' || $_SERVER['REMOTE_ADDR'] == '172.16.0.116' ){ ?>
									<br />
									<span class="f_l"><button type='button' class='btn btn-default btn-default-setting' style="font-size:12px;padding:0;" data-setting='1|30|'>개발_30분</button></span>
								<? } ?>
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
                                    <label for="TargetChildMode" class="hidden">업무구분</label>
                                    <select class="form-control noh_text_14 w_100" id="TargetChildMode" name="ChildMode">
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
                                    <input type="number" name="Rate" class="form-control only_digit_str" value="<?=$ProjectData['Rate']?>" <?=($LoggedInfo['role'] == ROLE_EMPLOYEE && $LoggedInfo['userId']!==$ProjectData['ToDoID']?'disabled':'')?> min="0" max="100" step="10">
                                </td>
                            </tr>
                            <tr>
                                <td class="table-dark text-center bg-gray">예상작업시간(분)</td>
                                <td class="text-left" colspan="3">
                                    <? if ( $ProjectData['Foretime'] > 0 ) {?>
                                        <input type="hidden" name="Foretime" id="TargetForetime" value="<?=$ProjectData['Foretime']?>" >
                                        <?=number_format($ProjectData['Foretime'])?>
                                        <? if ($ProjectData['Foretime']>60 ) echo " (".number_format($ProjectData['Foretime']/60,1)." 시간)"?>
                                    <? }else{?>
                                        <span class="f_l w_30">
                                            <input type="number" name="Foretime" id="TargetForetime" class="form-control only_digit_str w_80" value="<?=$ProjectData['Foretime']?>" placeholder="최초등록후 수정불가">
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
                                <td >
                                    <label for="TargetBackground" class="hidden">색상정보</label>

									<!-- //TODO : 실적용시 NAME 변경-->
                                    <select class="form-control noh_text_14 w_30" id="TargetBackground" name="Background1">
                                        <option value="" class="bg-post-default">기본색</option>
                                        <? foreach ( $CommonCode['WorkBackground'] as $key => $val ) {?>
                                            <option value="<?=$val['class']?>" <?=selected($val['class']."2", @$ProjectData['Background']."2")?>  class="<?=$val['class']?>2"><?=$val['name']?></option>
                                        <?} ?>
                                    </select>
									<button type='button' id="font_color" class="jscolor {valueElement:'bacground-value1', onFineChange:'setTextColor(this)'}" >
									색상선택
									</button>
									<input id="bacground-value1" value="<?=@$ProjectData['Background']?>" style='width:70px; text-align: center;' name="Background"> 
									<button type='button' name='initColor' onclick="document.getElementById('font_color')
    .jscolor.fromString('000000')" >초기화</button>
                                </td>
                                <td class="table-dark text-center bg-gray">배경색상</td>
                                <td id="jscolor_view_td">
                                    <label for="TargetPostColor" class="hidden">색상정보</label>

									<!-- //TODO : 실적용시 NAME 변경-->
                                    <select class="form-control noh_text_14 w_30" id="TargetPostColor" name="PostColor1">
                                        <option value="" class="bg-post-default2">기본색</option>
                                        <? foreach ( $CommonCode['WorkPostColor'] as $key => $val ) {?>
                                            <option value="<?=$val['class']?>" <?=selected($val['class'], @$ProjectData['PostColor'])?>  class="<?=$val['class']?>"><?=$val['name']?></option>
                                        <?} ?>
                                    </select>
									<button type='button' id="bg_color" class="jscolor {valueElement:'bacground-value2', onFineChange:'setTextColor(this)'}"  >
									색상선택
									</button>
									<input id="bacground-value2" value="<?=@$ProjectData['PostColor']?>" style='width:70px; text-align: center;' name="PostColor"> 
                                
									<button type='button' name='initColor' onclick="document.getElementById('bg_color')
    .jscolor.fromString('FFFF88')" >초기화</button>
								</td>
                            </tr>

                            <tr>
                                <td class="table-dark text-center bg-gray">인트라넷</td>
                                <td colspan="3"><input type="text" id="IntraUrl" name="IntraUrl" class="form-control" placeholder="인트라넷 바로기기 주소 입력" value="<?=$ProjectData['IntraUrl']?>" <?=$ProjectData['IntraBoard']?"disabled":""?>></td>
                            </tr>
                            <tr>

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

		// 기존데이터
		var b_color_class = [ 		
			'bg-post-default2'    
			,'bg-post-green2'   
			,'bg-post-yeondo2'   
			,'bg-post-skyblue2'   
			,'bg-post-pink2'   
			,'bg-post-gray2'   
			,'bg-post-red2'   
			,'bg-post-black2'   
			,'bg-post-white2'   
			,'bg-post-orange2'   // until index 9 background-color
			,'bg-post-default'   // after index 10 font-color
			,'bg-post-green'   
			,'bg-post-skyblue'   
			,'bg-post-yeondo'  
			,'bg-post-pink'   
			,'bg-post-red'   
			,'bg-post-gray'   
			,'bg-post-white'   
			,'bg-post-black'   
		];
		var b_color = ['#ffff88','#008d4c','#d9da42','#e1fcfe','#fbe2e7','#d1d1d1','#ff3f48','#000000','#ffffff','#ff9a25'
			,'#000000','#008d4c','#e1fcfe','#bada3f','#fbe2e7','#ff3f48','#d1d1d1','#ffffff','#000000'
			];

		// 색상표 추가 20200226 Start
		var $selct = $('.form-control.noh_text_14.w_30');
		if($selct != undefined ) {
			$selct.each(function(i,e){	 //index 0 => 글자색 Background , index 1 => 배경색   PostColor	
				var color_class = $(e).val();
				if($(e).next().next('input').val() == '') { 
					var $obj;
					if( i == 0) {
						$obj = $('input[name=Background]');  
						color_class = (color_class == '') ? 'bg-post-default' : color_class ;
					}else {
						$obj = $('input[name=PostColor]');  
						color_class = (color_class == '') ? 'bg-post-default2' : color_class;
					}
					for( var j=0; j < b_color_class.length ; j++ ) {		
						if(color_class == b_color_class[j]) {
							$obj.val(b_color[j]);
						}
					}
				}
			}); //end foreach
			$selct.css('display','none');  
		}
		$('button[name=initColor]').on('click', function(){	
			var $c_nput = $(this).prev();
			if($c_nput.attr('name') == 'Background') {
				$c_nput.val('000000');
			}else {
				$c_nput.val('FFFF88');
			}
		});
		// 색상표 추가 20200226 End

    }); // end function
	function setTextColor(picker) {
		if($(picker.styleElement).attr('id') == 'font_color') {
			//console.log($(picker.styleElement).attr('id'));
			//$('.jscolor ').css('color', '#'+picker.toString());
		}else {
			//$('.jscolor ').css('background-color', '#'+picker.toString());
		}
	}

</script>

<script type="text/javascript">
    jQuery(function($) {
        $(".modal-title").text("프로젝트 업무 정보.");
        <? if ($ProjectData['Status'] == 1 && $ProjectData['SUMDoingTime'] == 0) { ?>
            $(".modal-body").prepend("<span class='f_r mb_10'><button type='button' class='btn btn-default mr_10 btn_modify'>수정</button><button type='button' class='btn btn-default btn_delete'>삭제</button></span>");
        $(".modal-footer").append("<span class='f_r mb_10'><button type='button' class='btn btn-default ml_10 btn_modify'>수정</button><button type='button' class='btn btn-default btn_delete'>삭제</button></span>");
        <?}else{ ?>
            $(".modal-body").prepend("<span class='f_r mb_10'><button type='button' class='btn btn-default mr_10 btn_modify'>수정</button></span>");
            $(".modal-footer").append("<button type='button' class='btn btn-default btn_modify'>수정</button>");
        <? }?>


        //$(".modal-footer").append("수정,삭제 버튼 위로 올라감");
        /*$(".modal-footer").append("<button type='button' class='btn btn-default' id='btn_modify'>수정</button>");
        $(".modal-footer").append("<button type='button' class='btn btn-default' id='btn_delete'>삭제</button>");*/

        $(".only_digit_str").bind("keyup",function(){
            $(this).val($(this).val().replace(/[^0-9]/g,""));
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
                            var xChildMode = $("#TargetChildMode").val();
                            var xForetime = $("#TargetForetime").val();
                            var xTargetBackground = $("#TargetBackground").val();
                            parent.ajax_statics_update(targetIdx,xChildMode,xForetime,xTargetBackground);
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
            let targetIdx = $('#ProjectWorkIdx').val();
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

                            parent.addtextcomment(targetIdx,json.lastcomment);
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
            let targetIdx = $('#ProjectWorkIdx').val();
            if(confirmation )
            {
                $.ajax({
                    type        : "POST",
                    dataType    : "json",
                    url         : "/manager/project/replyinsert",
                    data        : {
                        "ProjectWorkIdx" : targetIdx,
                        "Comment" : encodeURI($('#w_commnet').val())
                    },
                    async: false,
                    success: function(json){
                        if ( json.result === true ) {
                            let addhtml = "<li class='f_l list-group-item d-flex justify-content-between align-items-center w_100'><span style='float:left;width:70% !important;min-width:70% !important;'>"+$("#w_commnet").val()+"</span><span class='badge badge-primary badge-pill btn_delete_reply noh_cursor' data-idx='"+json.result_idx+"'>X</span><span class='badge badge-primary badge-pill'>"+json.RegName + "</span><span class='badge badge-primary badge-pill'>"+json.RegDatetime + "</span></li>";
                            $("#Pop_Wrapper_Replylist").append(addhtml);
                            let sendtext  = "최근메모 : " + $("#w_commnet").val();
                            parent.addtextcomment(targetIdx,sendtext);
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
            let targetIdx = $('#ProjectWorkIdx').val();
            let confirmation201907291121 = confirm("수정하시겠습니까?");
            if(confirmation201907291121)
            {
                $("#ModifyMode").val(1);
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
                            //var xChildMode = $("#TargetChildMode").val();
                            //var xForetime = $("#TargetForetime").val();
                            //var xTargetBackground = $("#TargetBackground").val();
                            //parent.ajax_statics_update(targetIdx,xChildMode,xForetime,xTargetBackground);
                            alert('수정되었습니다.');
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

			 $(document).on('click', '.btn-default-setting',function() {
				var valArray = $(this).attr("data-setting").split("|");

				//기타
				// TargetChildMode
				//시간
				// TargetForetime
				//색상
				// TargetPostColor

				$('#TargetChildMode').val(valArray[0]);
				$('#TargetForetime').val(valArray[1]);
				$('#TargetPostColor').val(valArray[2]);
			 
			 });
    })
</script>
<?include_once  $_SERVER["DOCUMENT_ROOT"].'/application/views/includes/popfooter.php'; ?>



