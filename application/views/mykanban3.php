<!-- Morris charts -->
<link type="text/css" href="<?php echo base_url(); ?>assets/node/css/chat2.css?v=<?=time()?>" rel="stylesheet">
<link type="text/css" href="<?php echo base_url(); ?>assets/node/css/jquery.mCustomScrollbar.css" rel="stylesheet">
<link type="text/css" href="<?php echo base_url(); ?>assets/node/css/emoji.css" rel="stylesheet">
<link href="<?php echo base_url(); ?>assets/kanban/dist/hackerskanban.css?v=<?=time()?>" rel="stylesheet" type="text/css" />
<style>
    #myKanban {overflow-x: auto;padding: 20px 0;}
    .success {background: #000000;color:#fff}
    .info {background: #605ca8;color:#fff !important}
    .warning {background: #137b12;color:#fff}
    .error {background: #ff0000;}
    .emoji-selector > li > a{ padding:0 !important;}
    .recently_comment{clear:both;width:100%;max-width:100%;padding:2px 0;text-overflow:ellipsis;overflow: hidden;}
    .intra_page_link{clear:both;width:100%;max-width:100%;padding:2px 0;cursor: pointer !important;}
</style>

<div class="content-wrapper" id="page_container">
    <div class="h_con TodoWrapper2 display_none" id="CanToDoStandbyArea" >
    </div>


    <section class="content-header" >
        <h1 id="mylnb_wrap">
            <i class="fa fa-tachometer" aria-hidden="true"></i> My Jobs&nbsp;&nbsp;&nbsp;&nbsp;
            <small>
                <!-- <i class="noh_cursor" data-toggle="tooltip" data-placement="top" title="Todo업무는 본인의 업무만 생성 가능하며 일일리포트는 Doing상태가 없어야 합니다.">
                  <img src="<?php /*echo base_url(); */?>assets/images/icon_question.png" alt="범례" />
              </i>-->
                <img src="<?php echo base_url(); ?>assets/images/emergency.png" height="20"> : 긴급처리 요망업무&nbsp;&nbsp;
                <img src="<?php echo base_url(); ?>assets/images/reopen.png" height="20"> : 재작업(ReOpen)
                <img src="<?php echo base_url(); ?>assets/images/together.png" height="20"> : 협업작업
            </small>
        </h1>
    </section>

    <section class="content">
        <input type="hidden" id="UID" value="<?=$LoginSession['userId']?>" >
        <input type="hidden" id="NowDoingCount" value="<?=count($MyWork[2])?>" >
        <input type="hidden" id="NowSourceName" value="" >
        <input type="hidden" id="NowTargetName" value="" >
        <input type="hidden" id="lastMessanger" value="">
        <input type="hidden" id="IsGoon" value="<?=(count($MyWork[2])>0?1:2)?>">
        <input type="hidden" id="IsDoneOk" value="1">


        <div class="row">
            <div class="col-lg-3 col-xs-6">
                <!-- small box -->
                <div class="small-box2 bg-purple">
                    <div class="inner">
                        <h3>대기업무 : <span  id="Target_My_doing_Count"><?=isset($TODOSTATICS['SUMCount'])?$TODOSTATICS['SUMCount']:0?></span></h3>
                        <h3>긴급업무 : <span  id="Target_My_doing_Count2"><?=isset($TODOSTATICS['SUMEmergency'])?$TODOSTATICS['SUMEmergency']:0?></span>&nbsp;&nbsp;&nbsp;재작업업무 : <span  id="Target_My_doing_Count3"><?=isset($TODOSTATICS['SUMReopen'])?$TODOSTATICS['SUMReopen']:0?></span></h3>
                        <p>예상소요시간합계  : <span  id="Target_My_doing_Count4"><?=isset($TODOSTATICS['SUMDForetime'])?number_format($TODOSTATICS['SUMDForetime']):0?>분
                                <?
                                if ( isset($TODOSTATICS['SUMDForetime'])) {
                                    if ($TODOSTATICS['SUMDForetime'] > 60) echo " ( " . ROUND($TODOSTATICS['SUMDForetime'] / 60, 1) . " 시간 )";
                                }?></span></p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-android-apps"></i>
                    </div>
                    <a class="small-box-footer">
                        To Do  현황
                        &nbsp;&nbsp;<span class="btn btn-xs btn-default" onclick="fn_move_myall();">내업무전체</span>
                    </a>
                </div>
            </div><!-- ./col -->
            <div class="col-lg-3 col-xs-6">
                <!-- small box -->
                <div class="small-box2 bg-gray-active">
                    <div class="inner">
                        <h3>금일완료업무 : <span  id="Target_My_done_Count"><?=isset($DONESTATICS['SUMCount'])?$DONESTATICS['SUMCount']:0?></span></h3>
                        <p>투입누적시간 : <span  id="Target_My_done_Count2"><?=isset($DONESTATICS['SUMDoingTime'])?number_format($DONESTATICS['SUMDoingTime']):0?>분
                                <?
                                if(isset($DONESTATICS['SUMDoingTime']) ) {
                                    if ($DONESTATICS['SUMDoingTime'] > 60) echo " ( " . ROUND($DONESTATICS['SUMDoingTime'] / 60, 1) . " 시간 )";
                                }?>
                        </span></p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-stats-bars"></i>
                    </div>
                    <a class="small-box-footer">Done 현황</a>
                </div>
            </div><!-- ./col -->
            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-yellow">
                    <div class="inner">
                        <h3>&nbsp;</h3>
                        <p>&nbsp;</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-person">&nbsp;</i>
                    </div>
                    <a  class="small-box-footer">&nbsp;</a>
                </div>
            </div><!-- ./col -->

            <div class="col-lg-3 col-xs-6 <?=($LoginSession['role'] == ROLE_ADMIN  )?'display_none':'' ?>">
                <!-- small box -->
                <div class="small-box bg-red">
                    <div class="inner">
                        <h3>업무리포트<?/*=isset($IsReported)?'등록완료':'미진행'*/?></h3>
                        <p>일일 -> 기간산정 업무리포트 변경</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-newspaper-o"></i>
                    </div>
                    <a href="#" class="small-box-footer">&nbsp;<span class="btn btn-xs btn-default" onclick="fn_todayreport('<?=isset($IsReported)?1:0?>');">업무리포트 작성<?=$IsReported?></span></a>
                </div>
            </div><!-- ./col -->
            <div class="col-lg-3 col-xs-6 <?=($LoginSession['role'] != ROLE_ADMIN  )?'display_none':'' ?>">
                <span class="btn btn-md btn-default" onclick="refresh_intrainfo(1);">인트라넷 정보 갱신(1)</span>
                <span class="btn btn-md btn-default" onclick="refresh_intrainfo(2);">인트라넷 정보 갱신(2)</span>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="col-md-12">
                    <!--<button class='btn btn-default' id="addDefault">Add "Default" board</button>-->
                    <!--<button class='btn btn-primary' id="addToDo">Todo 추가</button>-->
                    <!--<button class='btn btn-default' id="removeBoard">Remove "Done" Board</button>
                    <button class='btn btn-default' id="removeElement">Remove "My Task Test"</button>-->
                </div>
            </div>
            <div class="col-lg-12">
                <div id="myKanban"></div>
            </div>
        </div>

        <div class="control-sidebar-bg"></div>
    </section>

    <div id="formreg_todo" class="ly_pop_top w700">
        <span class="bg"></span>
        <span class="wrap" style="top:30%">
            <form name="TodoRegFrom" id="TodoRegFrom"  method="POST">
            <input type="hidden" name="TodoID" value="<?=$LoginSession['userId']?>">
            <input type="hidden" name="RegID" value="<?=$LoginSession['userId']?>">
			<button type="button" class="popcls"><img src="<?php echo base_url(); ?>assets/images/ico_x.gif" alt="창닫기"></button>
			<h2>Todo 업무추가 </h2>
			<div class="elm">

				<fieldset class="tbl_type2">
					<table>
						<colgroup>
							<col style="width:20%;">
							<col style="width:25%;">
                            <col style="width:25%;">
							<col style="width:30%;">
						</colgroup>
						<tbody>
							<tr>
								<th scope="row">프로젝트 선택</th>
								<td class="spce" colspan="3">
                                    <div class="input-group padding0 ">
                                        <label for="ProjectIdx" class="hidden">프로젝트 선택</label>
                                        <select class="form-control noh_text_14 w_100" id="ProjectIdx" name="ProjectIdx">
                                        <? foreach ( $ProjectList as  $key => $val ) {?>
                                            <option value="<?=$val['ProjectIdx']?>"> <?=$val['ProjectTitle']?></option>
                                        <? } ?>
                                        </select>
                                    </div>
                                </td>
							</tr>
							<tr>
								<th scope="row">ToDo업무</th>
								<td class="spce" colspan="3">
                                    <span class="input-group padding0 w_100">
                                        <input type="text" name="title" id="title" class="form-control border-0 noh_text_14 w_100" placeholder="업무 타이틀을 입력하세요">
                                        <span class="md-line"></span>
                                    </span>
                                </td>
							</tr>
                            <tr>
								<th scope="row">업무구분</th>
								<td class="spce" >
                                    <div class="input-group padding0 ">
                                        <label for="ChildMode" class="hidden">업무구분</label>
                                        <select class="form-control noh_text_14 w_100" id="ChildMode" name="ChildMode">
                                            <option value="">없음</option>
                                            <? foreach ( $CommonCode['ChildMode'] as $key => $val ) {?>
                                                <option value="<?=$key?>"><?=$val['name']?></option>
                                            <?} ?>
                                        </select>
                                    </div>
                                </td>
                                <th scope="row">우선순위</th>
								<td class="spce">
                                    <div class="input-group padding0 ">
                                        <label for="Priority" class="hidden">우선순위 선택</label>
                                        <select class="form-control noh_text_14 w_100" id="Priority" name="Priority">
                                            <? foreach ( $CommonCode['Priority'] as $key => $val ) {?>
                                                <option value="<?=$key?>"><?=$val['name']?></option>
                                            <?} ?>
                                        </select>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">예상작업시간(분)</th>
                                <td class="text-left" colspan="3">
                                    <span class="f_l w_20">
                                        <input type="number" name="Foretime"  id="Foretime" class="form-control only_digit_str" value="10" step="10">
                                    </span>
                                    <span class="f_l w_80">
                                        <span class="f_l w_10"><button type='button' class='btn btn-default btn-set-time' data-time="10">10분</button></span>
                                        <span class="f_l w_10"><button type='button' class='btn btn-default btn-set-time' data-time="30">30분</button></span>
                                        <span class="f_l w_10"><button type='button' class='btn btn-default btn-set-time' data-time="60">1H</button></span>
                                        <span class="f_l w_10"><button type='button' class='btn btn-default btn-set-time' data-time="120"'>2H</button></span>
                                        <span class="f_l w_10"><button type='button' class='btn btn-default btn-set-time' data-time="240"'>4H</button></span>
                                        <span class="f_l w_10"><button type='button' class='btn btn-default btn-set-time' data-time="480"'>1일</button></span>
                                        <span class="f_l w_10"><button type='button' class='btn btn-default btn-set-time' data-time="960"'>2일</button></span>
                                        <span class="f_l w_10"><button type='button' class='btn btn-default btn-set-time' data-time="1440"'>3일</button></span>
                                        <span class="f_l w_10"><button type='button' class='btn btn-default btn-set-time' data-time="1920"'>4일</button></span>
                                        <span class="f_l w_10"><button type='button' class='btn btn-default btn-set-time' data-time="2400"'>5일</button></span>
                                    </span>
                                </td>
                            </tr>
						</tbody>
					</table>
				</fieldset>

				<div class="col-md-12 text-center mt-2 mb-3">
                    <button type="submit" onclick="" class="btn btn-md btn-default">Todo업무 생성</button>
                </div>
			</div>
            </form>
		</span>
        <span class="blank"></span>
    </div>

</div>
<link type="text/css" href="<?php echo base_url(); ?>assets/dist/css/jquery-ui.css" rel="stylesheet" />
<script src="<?php echo base_url(); ?>assets/kanban/dist/hackerskanban.js"></script>
<script>



    $(document).on("click", ".btn-set-time", function () {
        $("#Foretime").val($(this).data('time'));
        return false;
    });

    $(document).on("click", ".popcls", function () {
        $('#formreg_todo').hide();
    });
    let DivwidthTmp = $( "#myKanban" ).width();
    let Divwidth = parseInt((DivwidthTmp-60)/3);
    $(window).resize(function(){
        DivwidthTmp = $( "#myKanban" ).width();
        Divwidth = parseInt((DivwidthTmp-60)/3);
        $(".kanban-container").css("width",DivwidthTmp);
        $(".kanban-board").css("width",Divwidth)

    });

    var KanbanTest = new HackersKanban({
        element: "#myKanban",
        gutter: "10px",
        widthBoard: Divwidth+"px",
        click: function(el) {
            console.log("Trigger on all items click!");
        },
        buttonClick: function(el, boardId) {
            var formItem = document.createElement("form");
            formItem.setAttribute("class", "itemform");
            formItem.innerHTML =
                '<div class="form-group"><textarea class="form-control" rows="2" autofocus></textarea></div><div class="form-group"><button type="submit" class="btn btn-primary btn-xs pull-right">추가</button><button type="button" id="CancelBtn" class="btn btn-default btn-xs pull-right">취소</button></div>';

            KanbanTest.addForm(boardId, formItem);
            formItem.addEventListener("submit", function(e) {
                e.preventDefault();
                var text = e.target[0].value;
                KanbanTest.addElement(boardId, {
                    title: text
                });
                formItem.parentNode.removeChild(formItem);
            });
            document.getElementById("CancelBtn").onclick = function() {
                formItem.parentNode.removeChild(formItem);
            };
        },
        addItemButton: false,
        boards: [
            {
                id: "_todo",
                title: "To Do <span class='f_r'><button class='btn btn-primary btn-xs display_none' id='btn_save_seq'>Todo 순서적용</button>&nbsp;<button class='btn btn-default btn-xs display_none' id='btn_cancle_seq'>Todo순서적용취소</button>&nbsp;<button class='btn btn-default btn-xs' id='btn_change_seq'>Todo 순서변경</button>&nbsp;<button class='btn btn-primary btn-xs' id='addToDo'>Todo 추가</button></span>",
                class: "info,good",
                dragTo: ["_working"],
                item: [
                ]
            },
            {
                id: "_working",
                title: "Doing",
                class: "warning",
                item: [
                ]
            },
            {
                id: "_done",
                title: "Done",
                class: "success",
                dragTo: null,
                /*dragTo: ["_working"],*/
                item: [

                ]
            }
        ]
    });

    function  removeitem(targetIdx) {
        $('.kanban-item[data-eid="'+ targetIdx +'"]').remove();
        return false;
    }

    function  addtextcomment(targetIdx, newcoomet) {
        $('#Comment_'+ targetIdx).text('');
        $('#Comment_'+ targetIdx).text(newcoomet);
        return false;
    }

    var toDoButton = document.getElementById("addToDo");
    toDoButton.addEventListener("click", function() {
        $('#formreg_todo').show();
    });


    <?php
    if ( count($MyWork[1]) > 0 ) {
    foreach( $MyWork[1] as $key => $val ) {
    //$faceurl = "http://lproject.hackers.com/assets/dist/img/noh.jpeg";
    $val['Priority'] == 9  ?$_isemergency = "<span class='emergency'></span>" : $_isemergency = "";
    $val['IsReOpen'] == 1  ?$_isIsReOpen = "<span class='reopen'></span>" : $_isIsReOpen = "";
    $val['IsReOpen'] == 2  ?$_istogether = "<span class='togetherwork'></span>" : $_istogether = "";
    $val['IntraUrl'] ?$_intranetURL = "&nbsp;&nbsp;<a class='intra_page_link' data-url='".$val['IntraUrl']."'>☞ 인트라넷 바로가기</a>" : $_intranetURL = "";
    ?>
    KanbanTest.addElement("_todo", {
        id: <?=$val['ProjectWorkIdx']?>,
        title: "<?=$_isemergency?><?=$_isIsReOpen?><?=$_istogether?><br /><span class='<?=$val['Background']?>' id='Background_<?=$val['ProjectWorkIdx']?>'>[<?=$val['ProjectTitle']?>]<br /><i class='fa fa-info-circle noh_cursor btn_click_info text-light-blue ' data-idx='<?=$val['ProjectWorkIdx']?>'></i>&nbsp;&nbsp;<b><?=htmlspecialchars($val['title'])?> </b><?=$_intranetURL?><br /><span class='recently_comment' id='Comment_<?=$val['ProjectWorkIdx']?>'><?=htmlspecialchars($val['Comment'])?></span><input type='hidden' id='ChildMode_<?=$val['ProjectWorkIdx']?>' value='<?=$val['ChildMode']?>'><input type='hidden' id='Foretime_<?=$val['ProjectWorkIdx']?>' value='<?=$val['Foretime']?>'></span>",
        drag: function(el, source) {
            fn_checkmust(source.parentNode.dataset.id,"<?=$val['Foretime']?>","<?=$val['ChildMode']?>","<?=$val['ProjectWorkIdx']?>");
            fn_checkdoing(source.parentNode.dataset.id);
            return false;
        },
        dragend: function(el) {
            //console.log("END DRAG: " + el.dataset.eid);
            return false;
        },
        drop: function(el,source) {
            console.log("Esource.parentNode.dataset.id " + source.parentNode.dataset.id);
            if ( $("#IsGoon").val() == 2 || ( $("#IsGoon").val() == 1 && source.parentNode.dataset.id != "_todo" ) ) {
                fn_dropaction(source.parentNode.dataset.id);
                fn_todo_update(el.dataset.eid);
            }
            return false;
        }
    });
    <?php }
    }?>
    <?php
    if ( count($MyWork[2]) > 0 ) {
    foreach( $MyWork[2] as $key => $val ) {
    $val['Priority'] == 9  ?$_isemergency = "<span class='emergency'></span>" : $_isemergency = "";
    $val['IsReOpen'] == 1  ?$_isIsReOpen = "<span class='reopen'></span>" : $_isIsReOpen = "";
    $val['IsReOpen'] == 2  ?$_istogether = "<span class='togetherwork'></span>" : $_istogether = "";
    $val['IntraUrl'] ?$_intranetURL = "&nbsp;&nbsp;<a class='intra_page_link' data-url='".$val['IntraUrl']."'>☞ 인트라넷 바로가기</a>" : $_intranetURL = "";
    ?>
    KanbanTest.addElement("_working", {
        id: <?=$val['ProjectWorkIdx']?>,
        title: "<?=$_isemergency?><?=$_isIsReOpen?><?=$_istogether?><br /><span class='<?=$val['Background']?>' id='Background_<?=$val['ProjectWorkIdx']?>'>[<?=$val['ProjectTitle']?>]<br /> <b><i class='fa fa-info-circle noh_cursor btn_click_info text-light-blue' data-idx='<?=$val['ProjectWorkIdx']?>'></i>&nbsp;&nbsp;<?=htmlspecialchars($val['title'])?></b><?=$_intranetURL?><br /><span class='recently_comment' id='Comment_<?=$val['ProjectWorkIdx']?>'><?=htmlspecialchars($val['Comment'])?></span></span>",
        drag: function(el, source) {
            fn_checkdoing(source.parentNode.dataset.id);
            return false;
        },
        dragend: function(el) {
            console.log("el",el);
            //fn_isconfirm(source.parentNode.dataset.id);
            return false;
        },
        drop: function(el,source) {
            fn_dropaction(source.parentNode.dataset.id);
            fn_todo_update(el.dataset.eid);
            return false;
        }
    });

    <?php }
    }?>

    <?php
    if ( count($MyWork[9]) > 0 ) {
    foreach( $MyWork[9] as $key => $val ) {
    $val['Priority'] == 9  ?$_isemergency = "<span class='emergency'></span>" : $_isemergency = "";
    $val['IsReOpen'] == 1  ?$_isIsReOpen = "<span class='reopen'></span>" : $_isIsReOpen = "";
    $val['IsReOpen'] == 2  ?$_istogether = "<span class='togetherwork'></span>" : $_istogether = "";
    $val['IntraUrl'] ?$_intranetURL = "&nbsp;&nbsp;<a class='intra_page_link' data-url='".$val['IntraUrl']."'>☞ 인트라넷 바로가기</a>" : $_intranetURL = "";
    ?>
    KanbanTest.addElement("_done", {
        id: <?=$val['ProjectWorkIdx']?>,
        title: "<?=$_isemergency?><?=$_isIsReOpen?><?=$_istogether?><br /><span class='<?=$val['Background']?>' id='Background_<?=$val['ProjectWorkIdx']?>'>[<?=$val['ProjectTitle']?>]<br /> <b><i class='fa fa-info-circle noh_cursor btn_click_info text-light-blue' data-idx='<?=$val['ProjectWorkIdx']?>'></i>&nbsp;&nbsp;<?=htmlspecialchars($val['title'])?> </b><?=$_intranetURL?><br /><span class='recently_comment' id='Comment_<?=$val['ProjectWorkIdx']?>'><?=htmlspecialchars($val['Comment'])?></span></span>",
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

    <?php }
    }?>

</script>
<script src="<?php echo base_url(); ?>assets/js/jquery-ui-1.12.1/jquery-ui.min.js"></script>
<script src="<?php echo base_url(); ?>assets/kanban/mykanban3.js?v=<?=time()?>"></script>
