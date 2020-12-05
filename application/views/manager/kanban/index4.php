
<link type="text/css" href="<?php echo base_url(); ?>assets/node/css/chat2.css?v=<?=time()?>" rel="stylesheet">
<link type="text/css" href="<?php echo base_url(); ?>assets/node/css/jquery.mCustomScrollbar.css" rel="stylesheet">
<link type="text/css" href="<?php echo base_url(); ?>assets/node/css/emoji.css" rel="stylesheet">
<link href="<?php echo base_url(); ?>assets/kanban/dist/hackerskanban2.css" rel="stylesheet" type="text/css" />
<style>
    .emoji-selector > li > a{ padding:0 !important;}
</style>
<div class="content-wrapper" id="page_container" >

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
            <li class="<?=($LoginSession->userdata('role') == ROLE_EMPLOYEE || $LoginSession->userdata('role') == ROLE_MANAGER ? 'display_none':'')?>"  style="vertical-align:top;">
                <span class="f_l w_50 <?=($LoginSession->userdata('role') == ROLE_ADMIN  ? '' : 'display_none')?>">
                    <select class="noh_text_12 w_100" id="ProjectGroup">
                        <option value="0">사업부선택</option>
                        <? foreach( @$BUSINESSCode as $key => $row) { ?>
                            <option value="<?=$row['code']?>" <?=selected($row['code'], $PARENT_GROUP)?>><?=$row['name']?></option>
                        <? }?>
                    </select>
                </span>
                <span class="f_l <?=($LoginSession->userdata('role') == ROLE_ADMIN  ? 'w_50' : 'w_100')?>" >
                    <select class="noh_text_12 w_100" id="ProjectTeam">
                        <? foreach( $GROUPCode as $key => $row) { ?>
                            <option value="<?=$key?>" <?=selected($key,$PAGE_GROUP_IDX)?>><?=$row?></option>
                        <? }?>
                    </select>
                </span>

            </li>
            <li class="<?=($IsRegistPermission !== true || $LoginSession->userdata('role') == ROLE_ADMIN ?'display_none':'')?>">
                <span class="ToDoStandbyAreaAfter"><span id="btn_add_todo" class="btn btn-xs btn-default btn-flat display_none">업무추가</span></span>
                <span class="ToDoStandbyAreaAfter"><span id="btn_set_todo" class="btn btn-xs btn-primary btn-flat display_none">업무할당</span></span>
                <span class="ToDoStandbyAreaAfter"><span id="btn_change_seq" class="btn btn-xs btn-default btn-flat display_none">대기업무순서조정</span></span>
                <span class="ToDoStandbyAreaAfter"><span id="btn_save_seq" class="btn btn-xs btn-primary btn-flat display_none">대기업무순서적용</span></span>
                <span class="ToDoStandbyAreaAfter"><span id="btn_cancle_seq" class="btn btn-xs btn-primary btn-flat display_none">대기업무순서적용취소</span></span>
                <span class="ToDoStandbyAreaAfter"><span id="btn_view_todo" class="btn btn-xs btn-default btn-flat" data-idx="<?=count($ToDoReady)?>">대기업무(<?=count($ToDoReady)?>개)열기</span></span>
            </li>
            <!--<li class="<?/*=($isTeamView === false ? 'display_none':'')*/?>">
                <span class="ToDoStandbyAreaAfter" id="users_count">현재 접속 : 0명</span>
                <span id="btn_chat" class="btn btn-xs btn-primary btn-flat btn_chat">팀채팅열기</span>
            </li>-->
        </ol>
    </section>

    <section class="content">
        <div class="h_con TodoWrapper thisdroppable" id="ToDoStandbyArea" >
        </div>

        <div class="h_con TodoWrapper2 display_none" id="CanToDoStandbyArea" ></div>
        <div class="h_con TodoWrapper2 display_none" id="CanToDoStandbyArea2" ></div>

        <div class="row minwidth1024">
            <div class="col-lg-12 ">
                <input type="hidden" id="UID" value="<?=$LoginSession->userdata('userId')?>" >
                <input type="hidden" id="NowNoticeIdx" value="<?=@$NoticeMessages['NoticeIdx']?>" >
                <input type="hidden" id="lastMessanger" value="">
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
                                            <main class="kanban-drag2" style="min-height:100px !important;">
                                                <? foreach( $trow['SUB'][2] as $tkey2 => $trow2 )  {
                                                    $trow2['IntraUrl'] ?$_intranetURL = "&nbsp;&nbsp;<a class='intra_page_link' data-url='".$trow2['IntraUrl']."'>☞ 인트라넷 바로가기</a>" : $_intranetURL = "";
                                                    ?>
                                                    <div class="kanban-item" style="cursor: pointer !important; ">
                                                        <?
                                                        $trow2['Priority'] == 9  ?$_isemergency = "<span class='emergency'></span>" : $_isemergency = "";
                                                        $trow2['IsReOpen'] == 1  ?$_isIsReOpen = "<span class='reopen'></span>" : $_isIsReOpen = "";
                                                        $trow2['IsReOpen'] == 2  ?$_istogether = "<span class='togetherwork'></span>" : $_istogether = "";
                                                        ?>
                                                        <?=$_isemergency?><?=$_isIsReOpen?><?=$_istogether?>
                                                        <br >
                                                        프로젝트 : <?=htmlspecialchars($trow2['ProjectTitle'])?> <br >
                                                        <span class="project_title_wrap"><i class='fa fa-info-circle  noh_cursor btn_click_info text-light-blue' data-idx='<?=$trow2['ProjectWorkIdx']?>'></i>&nbsp;<?=htmlspecialchars($trow2['ProjectWorkTitle'])?><?=$_intranetURL?></span>
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
                                        <main class="kanban-drag2" style="min-height:100px !important;">
                                            <? if ( empty($trow['SITE_IDX']) ) {?>
                                                <span class="not_join_member">미등록 직원</span>
                                            <?}else if( $trow['ScheduleIdx'] ) {?>
                                                <span class="not_join_member">부재중 직원(휴가 또는 조퇴)</span>
                                            <? } ?>
                                        </main>
                                    </div>
                                <? } ?>

                                <? if ( isset($trow['SUB'][1])) {?>
                                    <div class="kanban-board">
                                        <header class="kanban-board-header info">
                                            <div class="kanban-title-board">
                                                To Do
                                                <span class='f_r <?=($IsRegistPermission !== true || $LoginSession->userdata('role') == ROLE_ADMIN ?'display_none':'')?>'>
                                                    <button class='btn btn-primary btn-xs btn_each_save_seq display_none' data-idx="<?=$trow['SITE_IDX']?>">순서적용</button>&nbsp;
                                                    <button class='btn btn-default btn-xs btn_each_cancle_seq display_none' data-idx="<?=$trow['SITE_IDX']?>">순서적용취소</button>&nbsp;
                                                    <button class="btn btn-default btn-xs btn_each_change_seq <?=count($trow['SUB'][1])>1?'':'display_none thisonlyone'?>" data-idx="<?=$trow['SITE_IDX']?>">순서변경</button>
                                                </span>
                                            </div>
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
                                                    <div class="kanban-item <?=$_isPermission?'Thisdraggable':''?> Thissortable<?=$trow['SITE_IDX']?>" style="cursor: pointer !important;" id="workid_<?=$trow2['ProjectWorkIdx']?>"  data-id="workid_<?=$trow2['ProjectWorkIdx']?>" data-idx="<?=$trow2['ProjectWorkIdx']?>"  data-parent="<?=$trow['SITE_IDX']?>">
                                                        <?
                                                        $trow2['Priority'] == 9  ?$_isemergency = "<span class='emergency'></span>" : $_isemergency = "";
                                                        $trow2['IsReOpen'] == 1  ?$_isIsReOpen = "<span class='reopen'></span>" : $_isIsReOpen = "";
                                                        $trow2['IsReOpen'] == 2  ?$_istogether = "<span class='togetherwork'></span>" : $_istogether = "";
                                                        ?>
                                                        <?=$_isemergency?><?=$_isIsReOpen?><?=$_istogether?>
                                                        <br >
                                                        프로젝트 : <?=htmlspecialchars($trow2['ProjectTitle'])?><br >
                                                        <span class="project_title_wrap "><i class='fa fa-info-circle noh_cursor btn_click_info text-light-blue' data-idx='<?=$trow2['ProjectWorkIdx']?>'></i>&nbsp;<span class="TargetProjectName"><?=htmlspecialchars($trow2['ProjectWorkTitle'])?><?=$_intranetURL?></span></span>
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
                                                        <span class="project_title_wrap"><i class='fa fa-info-circle  noh_cursor btn_click_info text-light-blue' data-idx='<?=$trow2['ProjectWorkIdx']?>'></i>&nbsp;<?=$trow2['ProjectWorkTitle']?><?=$_intranetURL?></span>
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
<script src="<?php echo base_url(); ?>assets/js/kanban/index4.js?v=<?=time()?>"></script>
