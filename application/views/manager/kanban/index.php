
<link type="text/css" href="<?php echo base_url(); ?>assets/node/css/chat.css?v=<?=time()?>" rel="stylesheet">
<link type="text/css" href="<?php echo base_url(); ?>assets/node/css/jquery.mCustomScrollbar.css" rel="stylesheet">
<link type="text/css" href="<?php echo base_url(); ?>assets/node/css/emoji.css" rel="stylesheet">
<link href="<?php echo base_url(); ?>assets/kanban/dist/hackerskanban2.css" rel="stylesheet" type="text/css" />
<style>
    .emoji-selector > li > a{ padding:0 !important;}
</style>
<div class="content-wrapper" id="page_container">

    <div class="h_con ChatUserLost_Wrapper" id="hidden_con">
        <ul id="hidden_join_list">
            <li>입장현황</li>
        </ul>
    </div>

    <div class="Chat_Wrapper display_none">
        <div class="r_con">
            <div class="room_top">
                <h3>팀채팅</h3>
                <a href="#" class="room_close"><img src="<?php echo base_url(); ?>assets/node/images/r_chat_close.gif" alt="닫기"></a>
            </div>

            <!-- 채팅창 -->
            <div class="room_box">
                <?php
                $_is_chat_nofi_view = "display_none";
                $_is_chat_nofi_view2 = "display_none";
                if ( isset($NoticeMessages['NoticeIdx']) ) $_is_chat_nofi_view = "";
                if ( isset($NoticeMessages['NoticeIdx']) ) {
                    if ( $NoticeMessages['RegID'] == $LoginSession->userdata('userId') || $LoginSession->userdata('role') < ROLE_EMPLOYEE ) {
                        $_is_chat_nofi_view2 = "";
                    }
                }
                ?>
                <div class="alarm_noti <?=$_is_chat_nofi_view?>" id="alarm_noti">
                    <h4 id="TargetNoticeTitle" class="notice_already"><?=@$NoticeMessages['Message']?></h4>
                    <a class="noti_close <?=$_is_chat_nofi_view?>" data-idx="<?=@$NoticeMessages['NoticeIdx']?>"><img src="<?php echo base_url(); ?>assets/node/images/r_chat_close.gif" alt="삭제"></a>
                </div>
                <div class="chatScrollH">
                    <ul id="chat_history">
                    </ul>
                </div>
                <div class="downScroll"><img src="<?php echo base_url(); ?>assets/node/images/r_down.png" alt="down"></div>
            </div>

            <div class="room_write">
                <ul class="check_noti">
                    <li><input type="checkbox" id="isNotice" name="isNotice">공지</li>
                    <li><a id="btn_show_userlist" class="noh_cursor">접속자현황</a></li>
                    <li><a  name="btn_clear" class="noh_cursor">메시지지우기</a></li>
                </ul>

                <div class="enter_write">
                    <div class="enter_write active" data-emojiarea><!-- textarea disabled 시 active 추가 해줘야 색상 변경 -->
                        <span class="emoji emoji-smile emoji-button">&#x1f604;</span>
                        <textarea id="message" name="message" rows="" cols="" maxlength="3000" placeholder=""></textarea>
                        <div class="textarea-clone"></div>
                    </div>
                    <button type="button" id="btn_enter" class="btn_enty"><img src="<?php echo base_url(); ?>assets/node/images/r_write.png" alt="입력"></button>
                </div>
            </div>
        </div>
    </div>

    <!-- todo regist form -->
    <div id="formreg_todo" class="ly_pop_top w800">
        <span class="bg"></span>
        <span class="wrap" style="top:30%">
            <form name="TodoRegFrom" id="TodoRegFrom"  method="POST">
			<button type="button" class="popcls"><img src="<?php echo base_url(); ?>assets/images/ico_x.gif" alt="창닫기"></button>
			<h2>Todo 업무추가 </h2>
			<div class="elm">

				<fieldset class="tbl_type2">
					<table>
						<colgroup>
							<col style="width:20%;">
							<col style="width:30%;">
                            <col style="width:20%;">
							<col style="width:30%;">
						</colgroup>
						<tbody>
							<tr>
								<th scope="row">프로젝트 선택</th>
								<td class="spce" colspan="3">
                                    <div class="input-group padding0 ">
                                        <input type="hidden" name="RegID" value="<?=$LoginSession->userdata('userId')?>">
                                        <label for="ProjectIdx" class="hidden">프로젝트 선택</label>
                                        <select class="form-control check_target noh_text_14 w_100" id="ProjectIdx" name="ProjectIdx">
                                        <? foreach ( $ProjectList as  $key => $val ) {?>
                                            <option value="<?=$val['ProjectIdx']?>"> <?=htmlspecialchars($val['ProjectTitle'])?></option>
                                        <? } ?>
                                        </select>
                                    </div>
                                </td>
							</tr>
							<tr>
								<th scope="row">ToDo업무</th>
								<td class="spce" colspan="3">
                                    <span class="input-group padding0 w_100">
                                        <input type="text" name="title" id="title" class="form-control border-0 noh_text_14 " placeholder="업무 타이틀을 입력하세요">
                                    </span>
                                </td>
							</tr>
                            <tr>
								<th scope="row">업무구분</th>
								<td class="spce" colspan="3">
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
                            </tr>
                            <tr>
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
							        <th scope="row">예상작업시간(분)</th>
                                    <td class="text-left" >
                                        <input type="number" name="Foretime" id="Foretime" class="form-control only_digit_str" value="10">
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

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa  fa-sitemap" aria-hidden="true"></i> <?=$NodeTeamName?> ( 팀원 : <?=$TeamMemberCount?>명 )
            <small>
                <img src="/assets/images/emergency.png" height="20"> : 긴급처리 요망업무&nbsp;&nbsp;
                <img src="/assets/images/reopen.png" height="20"> : 재작업(ReOpen)&nbsp;&nbsp;
                <img src="/assets/images/together.png" height="20"> : 협업작업
            </small>
        </h1>
        <ol class="breadcrumb">
            <li class="<?=($LoginSession->userdata('role') == ROLE_EMPLOYEE || $LoginSession->userdata('role') == ROLE_MANAGER ? 'display_none':'')?>">
                <select class="noh_text_14 w_100" id="ProjectTeam">
                    <? foreach( $GROUPCode as $key => $row) { ?>
                    <option value="<?=$key?>" <?=selected($key,$PAGE_GROUP_IDX)?>><?=$row?></option>
                    <? }?>
                </select>
            </li>
            <li class="<?=($IsRegistPermission !== true?'display_none':'')?>">
                <span class="ToDoStandbyAreaAfter"><span id="btn_add_todo" class="btn btn-xs btn-primary btn-flat">업무추가</span></span>
            </li>
            <li class="<?=($isTeamView === false ? 'display_none':'')?>">
                <span class="ToDoStandbyAreaAfter" id="users_count">현재 접속 : 0명</span>
                <span id="btn_chat" class="btn btn-xs btn-primary btn-flat btn_chat">팀채팅열기</span>
            </li>
        </ol>
    </section>

    <section class="content">
        <div class="row minwidth1024">
            <div class="col-lg-12 ">
                <input type="hidden" id="UID" value="<?=$LoginSession->userdata('userId')?>" >
                <input type="hidden" id="NowNoticeIdx" value="<?=@$NoticeMessages['NoticeIdx']?>" >
                <input type="hidden" id="lastMessanger" value="">
                <div class="col-md-12">
                    <div class="kanban-drag2 thisdroppable ToDoStandbyArea <?=($IsRegistPermission !== true?'display_none':'')?>" id="ToDoStandbyArea" >
                        <p>대기업무</p>
                        <? if ( count($ToDoReady) > 0 ) { ?>
                            <?  foreach( $ToDoReady as $tkey => $trow) {?>
                                <div class="kanban-item Thisdraggable" style="cursor: pointer !important;" id="workid_<?=$trow['ProjectWorkIdx']?>">
                                    <? if ( $trow['Priority'] == 9 ) { ?>
                                        <span class="emergency"></span>
                                    <? } ?>
                                    <? if ( $trow['IsReOpen'] == 1 ) { ?>
                                        <span class="reopen"></span>
                                    <? } ?>
                                    <? if ( $trow['IsReOpen'] == 2 ) { ?>
                                        <span class="togetherwork"></span>
                                    <? } ?>
                                    <br >
                                    프로젝트 : <?=htmlspecialchars($trow['ProjectTitle'])?><br >
                                    <span class="project_title_wrap"><i class='fa fa-info-circle noh_cursor btn_click_info' data-idx='<?=$trow['ProjectWorkIdx']?>'></i><span class="TargetProjectName"><?=htmlspecialchars($trow['title'])?></span></span>
                                </div>
                            <? } ?>
                        <? } ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12" id="TeamProjectArea" style="text-align:center">
                <ul class="kanban_wrapper" id="teamKanban">
                    <input type="hidden" id="UID" value="<?=$LoginSession->userdata('userId')?>" >
                <?php if( count($TeamMeberList) > 0 ) { ?>
                    <? foreach( $TeamMeberList as $tkey => $trow )  {
                        $_isPermission = false;
                        if ( $LoginSession->userdata('role') != ROLE_EMPLOYEE && $trow['SITE_IDX'] && empty($trow['ScheduleIdx']) ) {
                            $_isPermission = true;
                        }
                        ?>
                    <li class="kanban-container">
                        <div class="kanban-board profilezone margin_top_0">
                            <span class="profile_image"><img src="<?=($trow['FACE_URL']===NULL?"http://hac.educamp.org/data/user/HAC_".$trow['USER_ID'].".jpg":$trow['FACE_URL'])?>" alt="<?=$trow['USER_NAME']?>"></span>
                            <?=$trow['USER_NAME']?> <?=$trow['CLASS_NAME']?> <br />[<?=$trow['GROUP_NAME']?>]
                        </div>
                        <? if ( isset($trow['SUB'][1])) {?>
                            <div class="kanban-board">
                                <header class="kanban-board-header info">
                                    <div class="kanban-title-board">To Do</div>
                                </header>
                                <main class="kanban-drag2 <?=$_isPermission?'thisdroppable':''?>"  id="<?=$trow['SITE_IDX']?>">
                                    <? if ( empty($trow['SITE_IDX']) ) {?>
                                        <span class="not_join_member">미등록 직원</span>
                                    <?}else if( $trow['ScheduleIdx'] ) {?>
                                        <span class="not_join_member">부재중 직원(휴가 또는 조퇴)</span>
                                    <? }else { ?>
                                        <? foreach( $trow['SUB'][1] as $tkey2 => $trow2 )  {
                                            $trow2['IntraUrl']  ?$_intranetURL = "&nbsp;&nbsp;<a class='intra_page_link' data-url='".$trow2['IntraUrl']."'>☞ 인트라넷 바로가기</a>" : $_intranetURL = "";
                                            ?>
                                            <div class="kanban-item <?=$_isPermission?'Thisdraggable':''?>" style="cursor: pointer !important;" id="workid_<?=$trow2['ProjectWorkIdx']?>">
                                                <?
                                                $trow2['Priority'] == 9  ?$_isemergency = "<span class='emergency'></span>" : $_isemergency = "";
                                                $trow2['IsReOpen'] == 1  ?$_isIsReOpen = "<span class='reopen'></span>" : $_isIsReOpen = "";
                                                $trow2['IsReOpen'] == 2  ?$_istogether = "<span class='togetherwork'></span>" : $_istogether = "";
                                                ?>
                                                <?=$_isemergency?><?=$_isIsReOpen?><?=$_istogether?>
                                                <br >
                                                프로젝트 : <?=htmlspecialchars($trow2['ProjectTitle'])?><br >
                                                <span class="project_title_wrap "><i class='fa fa-info-circle fa-2x noh_cursor btn_click_info text-light-blue' data-idx='<?=$trow2['ProjectWorkIdx']?>'></i>&nbsp;<span class="TargetProjectName"><?=htmlspecialchars($trow2['ProjectWorkTitle'])?><?=$_intranetURL?></span></span>
                                            </div>
                                        <?}?>
                                    <? } ?>
                                </main>

                            </div>
                        <? }else{ ?>
                            <div class="kanban-board">
                                <header class="kanban-board-header info">
                                    <div class="kanban-title-board">To Do</div>
                                </header>
                                <main class="kanban-drag2 <?=$_isPermission?'thisdroppable':''?>"  id="<?=$trow['SITE_IDX']?>">
                                    <? if ( empty($trow['SITE_IDX']) ) {?>
                                        <span class="not_join_member">미등록 직원</span>
                                    <?}else if( $trow['ScheduleIdx'] ) {?>
                                        <span class="not_join_member">부재중 직원(휴가 또는 조퇴)</span>
                                    <? } ?>
                                </main>
                            </div>
                        <? } ?>

                        <? if ( isset($trow['SUB'][2])) {?>
                            <div class="kanban-board">
                                <header class="kanban-board-header warning">
                                    <div class="kanban-title-board">Doing</div>
                                </header>
                                <? if ( empty($trow['SITE_IDX']) ) {?>
                                    <span class="not_join_member">미등록 직원</span>
                                <?}else if( $trow['ScheduleIdx'] ) {?>
                                    <span class="not_join_member">부재중 직원(휴가 또는 조퇴)</span>
                                <? }else{ ?>
                                <main class="kanban-drag2">
                                    <? foreach( $trow['SUB'][2] as $tkey2 => $trow2 )  {
                                        $trow2['IntraUrl'] ?$_intranetURL = "&nbsp;&nbsp;<a class='intra_page_link' data-url='".$trow2['IntraUrl']."'>☞ 인트라넷 바로가기</a>" : $_intranetURL = "";
                                        ?>
                                        <div class="kanban-item" style="cursor: pointer !important;">
                                            <?
                                            $trow2['Priority'] == 9  ?$_isemergency = "<span class='emergency'></span>" : $_isemergency = "";
                                            $trow2['IsReOpen'] == 1  ?$_isIsReOpen = "<span class='reopen'></span>" : $_isIsReOpen = "";
                                            $trow2['IsReOpen'] == 2  ?$_istogether = "<span class='togetherwork'></span>" : $_istogether = "";
                                            ?>
                                            <?=$_isemergency?><?=$_isIsReOpen?><?=$_istogether?>
                                            <br >
                                            프로젝트 : <?=htmlspecialchars($trow2['ProjectTitle'])?> <br >
                                            <span class="project_title_wrap"><i class='fa fa-info-circle fa-2x noh_cursor btn_click_info text-light-blue' data-idx='<?=$trow2['ProjectWorkIdx']?>'></i>&nbsp;<?=htmlspecialchars($trow2['ProjectWorkTitle'])?><?=$_intranetURL?></span>
                                        </div>
                                    <? } ?>
                                </main>
                                <? } ?>
                            </div>
                        <? }else{ ?>
                            <div class="kanban-board">
                                <header class="kanban-board-header warning">
                                    <div class="kanban-title-board">Doing</div>
                                </header>
                                <main class="kanban-drag2">
                                    <? if ( empty($trow['SITE_IDX']) ) {?>
                                        <span class="not_join_member">미등록 직원</span>
                                    <?}else if( $trow['ScheduleIdx'] ) {?>
                                        <span class="not_join_member">부재중 직원(휴가 또는 조퇴)</span>
                                    <? } ?>
                                </main>
                            </div>
                        <? } ?>

                        <? if ( isset($trow['SUB'][9])) {?>
                            <div class="kanban-board">
                                <header class="kanban-board-header success">
                                    <div class="kanban-title-board">Done</div>
                                </header>
                                <? if ( empty($trow['SITE_IDX']) ) {?>
                                    <span class="not_join_member">미등록 직원</span>
                                <?}else if( $trow['ScheduleIdx'] ) {?>
                                    <span class="not_join_member">부재중 직원(휴가 또는 조퇴)</span>
                                <? }else{ ?>
                                <main class="kanban-drag2">
                                    <? foreach( $trow['SUB'][9] as $tkey2 => $trow2 )  {
                                        $trow2['IntraUrl']  ?$_intranetURL = "&nbsp;&nbsp;<a class='intra_page_link' data-url='".$trow2['IntraUrl']."'>☞ 인트라넷 바로가기</a>" : $_intranetURL = "";
                                        ?>
                                        <div class="kanban-item" style="cursor: pointer !important;">
                                            <?
                                            $trow2['Priority'] == 9  ?$_isemergency = "<span class='emergency'></span>" : $_isemergency = "";
                                            $trow2['IsReOpen'] == 1  ?$_isIsReOpen = "<span class='reopen'></span>" : $_isIsReOpen = "";
                                            ?>
                                            <?=$_isemergency?><?=$_isIsReOpen?>
                                            <br >
                                            프로젝트 : <?=htmlspecialchars($trow2['ProjectTitle'])?> <br >
                                            <span class="project_title_wrap"><i class='fa fa-info-circle fa-2x noh_cursor btn_click_info text-light-blue' data-idx='<?=$trow2['ProjectWorkIdx']?>'></i>&nbsp;<?=$trow2['ProjectWorkTitle']?><?=$_intranetURL?></span>
                                        </div>
                                    <? } ?>
                                </main>
                                <? }?>
                            </div>
                        <? }else{ ?>
                            <div class="kanban-board">
                                <header class="kanban-board-header success">
                                    <div class="kanban-title-board">Done</div>
                                </header>
                                <main class="kanban-drag2">
                                    <? if ( empty($trow['SITE_IDX']) ) {?>
                                        <span class="not_join_member">미등록 직원</span>
                                    <?}else if( $trow['ScheduleIdx'] ) {?>
                                        <span class="not_join_member">부재중 직원(휴가 또는 조퇴)</span>
                                    <? } ?>
                                </main>
                            </div>
                        <? } ?>
                    </li>
                    <? } ?>
                <? } ?>
                </ul>
                <p>
                    <span id="prevBtn" class="thisSliderBtn noh_cursor"><img src="<?php echo base_url(); ?>assets/images/left.png" alt="이전 버튼"></span>
                </p>
                <p>
                    <span id="nextBtn"  class="thisSliderBtn noh_cursor"><img src="<?php echo base_url(); ?>assets/images/right.png" alt="다음 버튼"></span>
                </p>

            </div>
        </div>

        <div class="control-sidebar-bg"></div>
    </section>
</div>
<script src="<?php echo base_url(); ?>assets/js/jquery-ui-1.12.1/jquery-ui.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/jquery.bxslider.js"></script>
<script src="<?php echo base_url(); ?>assets/js/kanban/index.js?v=<?=time()?>"></script>

<?if ( $isTeamView ) { ?>
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/node/js/main.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/node/js/jquery.emojiarea.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/node/js/jquery.mCustomScrollbar.concat.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/idle-timer.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/underscore-min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/utils.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/utils.extend.js"></script>
    <? if ( ENVIRONMENT === "production") { ?>
        <script type="text/javascript" src="http://<?=NODE_SERVER_CIP?>/js/socket.io/1.7.2/socket.io.min.js" id="socket"></script>
    <? }else{ ?>
        <script type="text/javascript" src="http://192.168.56.1:3001/js/socket.io/1.7.2/socket.io.min.js" id="socket"></script>
    <? } ?>
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/node/js/placeholders.min.js"></script>
    <script>
        /* 저장 기능 */
        var IsChatView = localStorage.getItem('IsChatView');
        if ( IsChatView == 'Y' ) {
            $(".Chat_Wrapper").removeClass('display_none');
            $(".btn_chat").text('팀채팅닫기');
            setTimeout(function() {
                $(".chatScrollH").mCustomScrollbar("scrollTo", "bottom", {
                    "scrollInertia": 0
                });
            }, 500);

        }else{
            $(".btn_chat").text('팀채팅열기');
            $(".Chat_Wrapper").addClass('display_none');

        }

        var IsPermission = "<?=$NodeTeamCode?>";
        /* 최근 메시지를 불러온다 */
        if ( IsPermission ) {
            jQuery.ajax({
                type: "POST",
                dataType: "json",
                url: "/manager/project/getChatMsg/2",
                data: "RoomIdx=" + IsPermission,
                async: false,
                beforeSend: function () {

                },
                success: function (json) {
                    if (json.totalCount > 0) {
                        for (var i = 0; i < json.messageList.length; i++) {
                            var html = "";
                            html += json.messageList[i]['MessageData'];
                            $("#chat_history").append(html);
                        }
                        var html2 = "";
                        html2 += "<li class='w_100 text-center'>------------ 최근 대화 ------------</li>";
                        $("#chat_history").append(html2);
                    }
                    return false;
                },
                complete: function () {

                }
            });
        }

        window.container = {
            "nickname" : '<?=$LoginSession->userdata('name')?>',
            "service_id": '<?=$NodeServiceID?>',
            "channel_id": '<?=$NodeChannelID?>',
            "channel": '<?=$NodeServiceID?>:<?=$NodeChannelID?>',
            "channel_status": 'play',
            "chat_room_idx": '<?=$NodeTeamCode?>',
            "connection": '//'+ document.getElementById('socket').src.split('/')[2] +'/',
            "is_auth": '1',
            "UID": "<?=$LoginSession->userdata('userId')?>",
            "user_idx": "<?=$LoginSession->userdata('userId')?>",
            "current_datetime": '<?=date("Y-m-d H:i:s")?>',
            "begin_datetime": '<?=date("Y-m-d H:i:s")?>',
            "wait_end_datetime": '',
            "max_message_count": 150,
            "message_count": 0,
            "is_user_count": 1,
            "users_count": 0,
            "notice": {},
        };


        $(function () {

            $( ".Chat_Wrapper" ).draggable({containment: "#page_container"}).resizable({
                containment: "#page_container"
            });

            $(document).on("click", "#btn_chat,.room_close", function () {
                $(".Chat_Wrapper").toggleClass("display_none");
                if ( $(".Chat_Wrapper").hasClass("display_none") ) {
                    $("#btn_chat").text('팀채팅열기');
                    localStorage.setItem('IsChatView', null);
                    $('#hidden_con').animate({
                        "margin-left":"-230"
                    },500,function(){
                        $('#btn_show_userlist').removeClass('none');
                    });
                }else{
                    // 채팅창 로드시 맨 아래로 이동
                    $(".chatScrollH").mCustomScrollbar("scrollTo", "bottom", {
                        "scrollInertia": 0
                    });
                    $("#btn_chat").text('팀채팅닫기');
                    localStorage.setItem('IsChatView', "Y");
                }
                return false;
            });
        });


    </script>
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/node/js/actions.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/node/js/broadcast.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/node/js/ui.js?v=<?=time()?>"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/node/js/index.js"></script>

<? }?>


